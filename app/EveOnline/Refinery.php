<?php

namespace App\EveOnline;

use App\Models\SDE\InvType;
use App\Models\Item;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\Eloquent\Collection;

/**
 * Handles calculating the materials gained from reprocessing items and
 * calculating the value of the buyback contract.
 */
class Refinery
{
	/**
	 * @var \Illuminate\Cache\Repository
	 */
	private $cache;

	/**
	 * @var \Carbon\Carbon
	 */
	private $carbon;

	/**
	 * @var \Illuminate\Config\Repository
	 */
	private $config;

	/**
	 * @var \App\Models\Item
	 */
	private $item;

	/**
	 * Constructs the class.
	 * @param  \Illuminate\Cache\Repository  $cache
	 * @param  \Carbon\Carbon                $carbon
	 * @param  \Illuminate\Config\Repository $config
	 * @param  \App\Models\Item              $item
	 * @return void
	*/
	public function __construct(Cache $cache, Carbon $carbon, Config $config, Item $item)
	{
		$this->cache  = $cache;
		$this->carbon = $carbon;
		$this->config = $config;
		$this->item   = $item;
	}

	/**
	 * Checks if an item can be bought as is.
	 * @param  InvType                        $type
	 * @param  \Illuminate\Support\Collection $items
	 * @return boolean
	 */
	public function canBeBoughtRaw(InvType $type, Collection $items)
	{
		$item = $items
			->where('typeID', (integer)$type->typeID)
			->first();

		return ($item && $item->buyRaw) == true;
	}

	/**
	 * Checks if an item can be recycled and all materials can be bought.
	 * @param  InvType                        $type
	 * @param  \Illuminate\Support\Collection $items
	 * @return boolean
	 */
	public function canBeRecycledAndBought(InvType $type, Collection $items)
	{
		return $this->checkIfItemMaterialsCanBeBought($type, $items, [
			'Charge', 'Commodity', 'Module', 'Ship'
		]);
	}

	/**
	 * Checks if an item can be refinedand all materials can be bought.
	 * @param  InvType                        $type
	 * @param  \Illuminate\Support\Collection $items
	 * @return boolean
	 */
	public function canBeRefinedAndBought(InvType $type, Collection $items)
	{
		return $this->checkIfItemMaterialsCanBeBought($type, $items, [
			'Asteroid'
		]);
	}

	/**
	 * Handles checking if an item's materials can all be bought.
	 * @param  InvType                        $type
	 * @param  \Illuminate\Support\Collection $items
	 * @param  array                          $categories
	 * @return boolean
	 */
	private function checkIfItemMaterialsCanBeBought(InvType $type, Collection $items, array $categories)
	{
		if (!in_array($type->group->category->categoryName, $categories)) {
			return false;
		}

		if ($type->materials->count() == 0) {
			return false;
		}

		foreach ($type->materials as $material) {
			$item = $items
				->where('buyRefined', true)
				->where('typeID', (integer)$material->materialTypeID)
				->first();

			if (!$item) {
				return false;
			}
		}

		return true;
	}

	private function getCategorizedItems($items, $buyback_items = null)
	{
		$result = [];

		foreach ($items as &$item) {
			if ($this->canBeBoughtRaw($item->type, $buyback_items)) {
				$result['raw'][] = $item;
				continue;
			}

			if ($this->canBeRecycledAndBought($item->type, $buyback_items)) {
				$result['recycled'][] = $item;
				continue;
			}

			if ($this->canBeRefinedAndBought($item->type, $buyback_items)) {
				$result['refined'][] = $item;
				continue;
			}

			$result['unwanted'][] = $item;
			continue;
		}

		return $result;
	}

	private function getInitializedMaterials($buyback_items)
	{
		$result = [];

		$materials = $buyback_items->filter(function ($item) {
			return !!$item->buyRecyled || !!$item->buyRefined;
		});

		foreach($materials as $material) {
			$result['materials'][$material->typeID] = 0;
		}

		return $result;
	}

	public function calculateBuyback($items, $buyback_items = null)
	{
		$result = [
			'raw'              => [],
			'refined'          => [],
			'recycled'         => [],
			'unwanted'         => [],
			'materials'        => [],
			'totalValue'       => 0.00,
			'totalValueModded' => 0.00,
		];

		if (!$items) {
			return (object)$result;
		}

		if (!$buyback_items) {
			$buyback_items = $this->item->with('type')->get();
		}

		$result = (object)array_merge($result,
			$this->getCategorizedItems    ($items, $buyback_items),
			$this->getInitializedMaterials(        $buyback_items)
		);

		// Recycle the recyclables and refine the refinables.
		foreach ($result->recycled as $item) {
			$materials = $this->getRecycledMaterials($item->type);
			$quantity  = (integer)($item->quantity / $item->type->portionSize);

			foreach ($materials as $key => $value) {
				$result->materials[$key] += (integer)($quantity * $value);
			}
		}

		foreach ($result->refined as $item) {
			$materials = $this->getRefinedMaterials($item->type);
			$quantity  = (integer)($item->quantity / $item->type->portionSize);

			foreach ($materials as $key => $value) {
				$result->materials[$key] += (integer)($quantity * $value);
			}
		}

		// Calculate the buyback value.
		$prices = $buyback_items->keyBy('typeID')->toArray();

		foreach ($result->raw as &$item) {
			$modifier = $prices[$item->type->typeID]['buyModifier'];
			$price    = $prices[$item->type->typeID]['buyPrice'   ];

			$result->totalValue       += $item->quantity * $price;
			$result->totalValueModded += $item->quantity * $price * $modifier;

			$buyback_item = $buyback_items->where('typeID', (integer)$item->type->typeID)->first();

			$item = (object)[
				'type'            => $item->type,
				'quantity'        => $item->quantity,

				'buyUnit'         => $buyback_item->buyPrice,
				'buyUnitModded'   => $buyback_item->buyPrice * $buyback_item->buyModifier,
				'buyTotal'        => $buyback_item->buyPrice * $item->quantity,
				'buyTotalModded'  => $buyback_item->buyPrice * $item->quantity * $buyback_item->buyModifier,

				'sellUnit'        => $buyback_item->sellPrice,
				'sellUnitModded'  => $buyback_item->sellPrice * $buyback_item->sellModifier,
				'sellTotal'       => $buyback_item->sellPrice * $item->quantity,
				'sellTotalModded' => $buyback_item->sellPrice * $item->quantity * $buyback_item->sellModifier,
			];
		}

		foreach ($result->materials as $materialTypeID => &$quantity) {
			$modifier = $prices[$materialTypeID]['buyModifier'];
			$price    = $prices[$materialTypeID]['buyPrice'   ];

			$result->totalValue       += $quantity * $price;
			$result->totalValueModded += $quantity * $price * $modifier;

			$buyback_item = $buyback_items->where('typeID', (integer)$materialTypeID)->first();

			$quantity = (object)[
				'type'            => $buyback_item->type,
				'quantity'        => $quantity,

				'buyUnit'         => $buyback_item->buyPrice,
				'buyUnitModded'   => $buyback_item->buyPrice * $buyback_item->buyModifier,
				'buyTotal'        => $buyback_item->buyPrice * $quantity,
				'buyTotalModded'  => $buyback_item->buyPrice * $quantity * $buyback_item->buyModifier,

				'sellUnit'        => $buyback_item->sellPrice,
				'sellUnitModded'  => $buyback_item->sellPrice * $buyback_item->sellModifier,
				'sellTotal'       => $buyback_item->sellPrice * $quantity,
				'sellTotalModded' => $buyback_item->sellPrice * $quantity * $buyback_item->sellModifier,
			];
		}

		return $result;
	}

	/**
	 * Gets the amount of materials an item has after applying the yield formula.
	 * @param  InvType $type
	 * @return array
	 */
	public function getRecycledMaterials(InvType $type)
	{
		$materials = [];
		$yield     = 0.52 * (1 + 0.02 * $this->config->get('refinery.scrapmetal')) * $this->config->get('refinery.station_tax');

		// Reprocess the item.
		foreach($type->materials as $material) {
			$materials[$material->materialTypeID] = $material->quantity * $yield;
		}

		return $materials;
	}

	/**
	 * Gets the amount of materials an item has after applying the yield formula.
	 * @param  InvType $type
	 * @return array
	 */
	public function getRefinedMaterials(InvType $type)
	{
		$materials = [];
		$yield     =      $this->config->get('refinery.station_tax'  )
			*             $this->config->get('refinery.station_yield')
			* (1 + 0.03 * $this->config->get('refinery.reprocessing'))
			* (1 + 0.02 * $this->config->get('refinery.efficiency'  ))
			* (1 + 0.01 * $this->config->get('refinery.beancounter' ))
		;

		// Find the base asteroid type without any adjectives.
		$skill = 'Ice';
		$bases = [
			'Arkonor',
			'Bistot',
			'Crokite',
			'Dark Ochre',
			'Gneiss',
			'Hedbergite',
			'Hemorphite',
			'Jaspet',
			'Kernite',
			'Mercoxit',
			'Omber',
			'Plagioclase',
			'Pyroxeres',
			'Scordite',
			'Spodumain',
			'Veldspar',
		];

		foreach ($bases as $base) {
			if (strpos($type->typeName, $base) === false) {
				continue;
			}

			$skill = $base;
			break;
		}

		$yield *= (1 + 0.02 * $this->config->get('refinery.'.strtolower($skill)));

		// Reprocess the item.
		foreach($type->materials as $material) {
			$materials[$material->materialTypeID] = $material->quantity * $yield;
		}

		return $materials;
	}
}
