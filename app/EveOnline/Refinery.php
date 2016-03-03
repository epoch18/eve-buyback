<?php

namespace App\EveOnline;

use App\Models\SDE\InvType;
use App\Models\Item;
use App\Models\Setting;

/**
 * Handles calculating the materials gained from reprocessing items and
 * calculating the value of the buyback contract.
 */
class Refinery
{
	/**
	* @var \App\Models\Item
	*/
	private $item;
	/**
	* @var \App\Models\SDE\InvType
	*/
	private $type;

	/**
	* Constructs the class.
	* @param  \App\Models\Item         $item
	* @param  \App\Models\SDE\InvType  $type
	* @return void
	*/
	public function __construct(Item $item, InvType $type)
	{
		$this->item     = $item;
		$this->type     = $type;
	}

	/**
	 * Checks if an item can be bought as is.
	 * @param  InvType $type
	 * @return boolean
	 */
	public function canBeBoughtRaw(InvType $type)
	{
		$item = $this->item
			->where('buyRaw', true)
			->where('typeID', $type->typeID)
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
		if (!in_array($type->group->category->categoryName, $categories)) {
			return false;
		}

		foreach ($type->materials as $material) {
			$item = $this->item
				->where('buyRefined', true)
				->where('typeID', $material->materialTypeID)
				->first();

			if (!$item) {
				return false;
			}
		}

		return true;
	}

	public function calculateBuyback($items)
	{
		// Sort the items into categories.
		$raw      = [];
		$refined  = [];
		$recycled = [];
		$unwanted = [];

		foreach ($items as $item) {
			if ($this->canBeBoughtRaw($item->type)) {
				$raw[] = $item;
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

		$materials = $this->item
			->where('buyRecycled', true)
			->get();

		foreach($materials as $material) {
			$result->materials[$material->typeID] = 0;
		}

		$materials = $this->item
			->where('buyRefined', true)
			->get();

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
			$modifier = $prices[$item->typeID]['buyModifier'];
			$price    = $prices[$item->typeID]['buyPrice'   ];

			$result->totalValue  += $quantity * $price;
			$result->totalModded += $quantity * $price * $modifier;
		}

		foreach ($result->materials as $materialTypeID => $quantity) {
			$modifier = $prices[$materialTypeID]['buyModifier'];
			$price    = $prices[$materialTypeID]['buyPrice'   ];

			$result->totalValue  += $quantity * $price;
			$result->totalModded += $quantity * $price * $modifier;
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
		$yield     = 0.52 * (1 + 0.02 * config('refinery.scrapmetal'));

		foreach($type->materials as $material) {
			$materials[$material->materialTypeID] = $material->quantity * $yield * config('refinery.station_tax');
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
		$yield     =      config('refinery.station_tax'  )
			*             config('refinery.station_yield')
			* (1 + 0.03 * config('refinery.reprocessing'))
			* (1 + 0.02 * config('refinery.efficiency'  ))
			* (1 + 0.01 * config('refinery.beancounter' ))
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

		$yield *= (1 + 0.02 * config('refinery.'.strtolower($skill)));

		// Reprocess the item.
		foreach($type->materials as $material) {
			$materials[$material->materialTypeID] = $material->quantity * $yield;
		}

		return $materials;
	}
}
