<?php

namespace App\EveOnline;

use App\Models\SDE\InvType;
use App\Models\Item;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Config\Repository as Config;

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
	* @var \Illuminate\Support\Collection
	*/
	private $items;

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

		$this->items  = $this->item->with('type')->get();
	}

	/**
	 * Checks if an item can be bought as is.
	 * @param  InvType $type
	 * @return boolean
	 */
	public function canBeBoughtRaw(InvType $type)
	{
		$item = $this->items
			->where('buyRaw', true)
			->where('typeID', (integer)$type->typeID)
			->first();

		return $item != null;
	}

	/**
	 * Checks if an item can be recycled and all materials can be bought.
	 * @param  InvType $type
	 * @return boolean
	 */
	public function canBeRecycledAndBought(InvType $type)
	{
		return $this->checkIfItemMaterialsCanBeBought($type, [
			'Charge', 'Commodity', 'Module', 'Ship'
		]);
	}

	/**
	 * Checks if an item can be refinedand all materials can be bought.
	 * @param  InvType $type
	 * @return boolean
	 */
	public function canBeRefinedAndBought(InvType $type)
	{
		return $this->checkIfItemMaterialsCanBeBought($type, ['Asteroid']);
	}

	/**
	 * Handles checking if an item's materials can all be bought.
	 * @param  InvType $type
	 * @param  array   $categories
	 * @return boolean
	 */
	private function checkIfItemMaterialsCanBeBought(InvType $type, array $categories)
	{
		if (!in_array($type->group->category->categoryName, $categories)) { return false; }

		$materials = $type->materials;
		if ($materials->count() == 0) { return false; }

		foreach ($materials as $material) {
			$item = $this->items
				->where('buyRefined', true)
				->where('typeID', (integer)$material->materialTypeID)
				->first();

			if (!$item) { return false; }
		}

		return true;
	}

	public function calculateBuyback($items)
	{
		if (!$items) {
			return (object)[
				'raw'         => [],
				'refined'     => [],
				'recycled'    => [],
				'unwanted'    => [],
				'materials'   => [],
				'totalValue'  => 0.00,
				'totalModded' => 0.00,
				'totalProfit' => 0.00,
			];
		}

		// Sort the items into categories.
		$raw      = [];
		$refined  = [];
		$recycled = [];
		$unwanted = [];

		foreach ($items as &$item) {
			if ($this->canBeBoughtRaw($item->type)) {
				$buyback_item = $this->items->where('typeID', (integer)$item->type->typeID)->first();

				$raw[] = (object)[
					'type'           => $item->type,
					'quantity'       => $item->quantity,

					'buyUnit'        => $buyback_item->buyPrice,
					'buyUnitModded'  => $buyback_item->buyPrice * $buyback_item->buyModifier,
					'buyTotal'       => $buyback_item->buyPrice * $item->quantity,
					'buyModded'      => $buyback_item->buyPrice * $item->quantity * $buyback_item->buyModifier,

					'sellUnit'       => $buyback_item->sellPrice,
					'sellUnitModded' => $buyback_item->sellPrice * $buyback_item->sellModifier,
					'sellTotal'      => $buyback_item->sellPrice * $item->quantity,
					'sellModded'     => $buyback_item->sellPrice * $item->quantity * $buyback_item->sellModifier,
				];

				continue;
			}

			if ($this->canBeRecycledAndBought($item->type)) {
				$recycled[] = $item;
				continue;
			}

			if ($this->canBeRefinedAndBought($item->type)) {
				$refined[] = $item;
				continue;
			}

			$unwanted[] = $item;
			continue;
		}

		// Initialize the results object that will be returned.
		$result = (object)[
			'raw'         => $raw,
			'refined'     => $refined,
			'recycled'    => $recycled,
			'unwanted'    => $unwanted,
			'materials'   => [],
			'totalValue'  => 0.00,
			'totalModded' => 0.00,
			'totalProfit' => 0.00,
		];

		$materials = $this->items
			->where('buyRecycled', true)
			->all();

		foreach($materials as $material) {
			$result->materials[$material->typeID] = 0;
		}

		$materials = $this->items
			->where('buyRefined', true)
			->all();

		foreach($materials as $material) {
			$result->materials[$material->typeID] = 0;
		}

		// Refine the refinables and recycle the recyclables.
		foreach ($refined as $item) {
			$materials = $this->getRefinedMaterials($item->type);
			$quantity  = (integer)($item->quantity / $item->type->portionSize);

			foreach ($materials as $key => $value) {
				$result->materials[$key] += (integer)($quantity * $value);
			}
		}

		foreach ($recycled as $item) {
			$materials = $this->getRecycledMaterials($item->type);
			$quantity  = (integer)($item->quantity / $item->type->portionSize);

			foreach ($materials as $key => $value) {
				$result->materials[$key] += (integer)($quantity * $value);
			}
		}

		// Calculate the buyback value.
		$prices = $this->item->get(['typeID', 'buyModifier', 'buyPrice'])->keyBy('typeID')->toArray();

		foreach ($result->raw as $item) {
			$modifier = $prices[$item->type->typeID]['buyModifier'];
			$price    = $prices[$item->type->typeID]['buyPrice'   ];

			$result->totalValue  += $item->quantity * $price;
			$result->totalModded += $item->quantity * $price * $modifier;
		}

		foreach ($result->materials as $materialTypeID => $quantity) {
			$modifier = $prices[$materialTypeID]['buyModifier'];
			$price    = $prices[$materialTypeID]['buyPrice'   ];

			$result->totalValue  += $quantity * $price;
			$result->totalModded += $quantity * $price * $modifier;
		}

		foreach ($result->materials as $typeID => &$value) {
			$item     = $this->items->where('typeID', $typeID)->first();
			$quantity = $value;

			$value = (object)[
				'type'           => $item->type,
				'quantity'       => $quantity,

				'buyUnit'        => $item->buyPrice,
				'buyUnitModded'  => $item->buyPrice * $item->buyModifier,
				'buyTotal'       => $item->buyPrice * $quantity,
				'buyModded'      => $item->buyPrice * $quantity * $item->buyModifier,

				'sellUnit'       => $item->sellPrice,
				'sellUnitModded' => $item->sellPrice * $item->sellModifier,
				'sellTotal'      => $item->sellPrice * $quantity,
				'sellModded'     => $item->sellPrice * $quantity * $item->sellModifier,
			];
		}

		$result->totalProfit = $result->totalValue - $result->totalModded;

		// Return the result;
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
