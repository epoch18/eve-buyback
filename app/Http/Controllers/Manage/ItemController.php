<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateItemsJob;
use App\Models\Item;
use App\Models\SDE\InvCategory;
use App\Models\SDE\InvGroup;
use App\Models\SDE\InvType;
use Illuminate\Http\Request;
use DB;

class ItemController extends Controller
{
	/**
	* @var \App\Models\Item
	*/
	private $item_model;

	/**
	* @var \App\Models\InvCategory
	*/
	private $category_model;

	/**
	* @var \App\Models\InvGroup
	*/
	private $group_model;

	/**
	* @var \App\Models\InvType
	*/
	private $type_model;

	/**
	* @var \App\Jobs\UpdateItemsJob
	*/
	private $update_items_job;

	/**
	* @var \Illuminate\Http\Request
	*/
	private $request;

	/**
	* Constructs the class.
	* @param  \App\Jobs\UpdateItemsJob    $update_items_job
	* @param  \App\Models\Item            $item_model
	* @param  \App\Models\SDE\InvCategory $category_model
	* @param  \App\Models\SDE\InvGroup    $group_model
	* @param  \App\Models\SDE\InvType     $type_model
	* @param  \Illuminate\Http\Request    $request
	*/
	public function __construct(
		UpdateItemsJob $update_items_job,
		Item           $item_model,
		InvCategory    $category_model,
		InvGroup       $group_model,
		InvType        $type_model,
		Request        $request
	) {
		$this->update_items_job = $update_items_job;
		$this->item_model       = $item_model;
		$this->category_model   = $category_model;
		$this->group_model      = $group_model;
		$this->type_model       = $type_model;
		$this->request          = $request;
	}

	/**
	 * Gets a list of buyback items.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getGetItems()
	{
		if (!$this->request->ajax()) { return $this->ajaxErrorResponse(); }

		return $this->item_model
			->with('type')
			->with('type.group')
			->with('type.group.category')
			->get()
			->toJson();
	}

	/**
	 * Handles adding new buyback items.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function postAddItems()
	{
		if (!$this->request->ajax()) { return $this->ajaxErrorResponse(); }

		// Get the properties that will be applied to the items.
		$properties = [
			'buyRaw'       => $this->request->input('buyRaw'      ) ? true : false,
			'buyRecycled'  => $this->request->input('buyRecycled' ) ? true : false,
			'buyRefined'   => $this->request->input('buyRefined'  ) ? true : false,
			'buyModifier'  => $this->request->input('buyModifier' ) ?      :  0.00,
			'sell'         => $this->request->input('sell'        ) ? true : false,
			'sellModifier' => $this->request->input('sellModifier') ?      :  0.00,
			'lockPrices'   => $this->request->input('lockPrices'  ) ? true : false,
			'buyPrice'     => 0.00,
			'sellPrice'    => 0.00,
		];

		$properties['buyModifier'] = is_numeric($properties['buyModifier'])
			? (double)$properties['buyModifier' ] : 0.00;

		$properties['sellModifier'] = is_numeric($properties['sellModifier'])
			? (double)$properties['sellModifier'] : 0.00;

		// Get the types being added.
		$ids   = $this->request->input('types') ?: [];
		$types = $this->type_model->whereIn('typeID', $ids)->get();

		// Get the types being added from a group.
		$ids    = $this->request->input('groups') ?: [];
		$groups = $this->group_model->with('types')->whereIn('groupID', $ids)->get();
		$groups->each(function ($group) use (&$types) {
			$group->types->each(function ($type) use (&$types) {
				$types->push($type);
			});
		});

		// Get the types being added from a category.
		$ids        = $this->request->input('categories') ?: [];
		$categories = $this->category_model->with('types')->whereIn('categoryID', $ids)->get();
		$categories->each(function ($category) use (&$types) {
			$category->types->each(function ($type) use (&$types) {
				$types->push($type);
			});
		});

		// Remove any duplicate types.
		$types = $types->unique('typeID');

		try {
			if ($types->count() == 0) {
				return $this->ajaxFailureResponse(
					trans('buyback.messages.add_items_nothing', $items->count()));
			}

			DB::transaction(function () use ($types, $properties) {
				$types->each(function ($type) use($properties) {
					// Ignore items that already exist.
					if (($item = $this->item_model->find($type->typeID))) { return; };

					// Add the type to the database as an item.
					$this->item_model->create([
						'typeID'       => $type->typeID,
						'typeName'     => $type->typeName,
						'buyRaw'       => $properties['buyRaw'      ],
						'buyRecycled'  => $properties['buyRecycled' ],
						'buyRefined'   => $properties['buyRefined'  ],
						'buyModifier'  => $properties['buyModifier' ],
						'buyPrice'     => $properties['buyPrice'    ],
						'sell'         => $properties['sell'        ],
						'sellModifier' => $properties['sellModifier'],
						'lockPrices'   => $properties['lockPrices'  ],
						'sellPrice'    => $properties['sellPrice'   ],
					]);
				});
			});

			return $this->ajaxSuccessResponse(
				trans_choice('buyback.messages.add_items_success', $types->count() == 1 ? 1 : 2));

		} catch (\Exception $e) {
			return $this->ajaxFailureResponse(
				trans_choice('buyback.messages.add_items_failure', $types->count() == 1 ? 1 : 2));
		}
	}

	/**
	 * Handles editing existing buyback items.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function postEditItems()
	{
		if (!$this->request->ajax()) { return $this->ajaxErrorResponse(); }

		// Get the properties that will be applied to all items.
		$properties = [
			'buyRaw'       => $this->request->input('buyRaw'      ) ? true : false,
			'buyRecycled'  => $this->request->input('buyRecycled' ) ? true : false,
			'buyRefined'   => $this->request->input('buyRefined'  ) ? true : false,
			'buyModifier'  => $this->request->input('buyModifier' ) ?      :  0.00,
			'sell'         => $this->request->input('sell'        ) ? true : false,
			'sellModifier' => $this->request->input('sellModifier') ?      :  0.00,
			'lockPrices'   => $this->request->input('lockPrices'  ) ? true : false,
		];

		$properties['buyModifier'] = is_numeric($properties['buyModifier'])
			? (double)$properties['buyModifier' ] : 0.00;

		$properties['sellModifier'] = is_numeric($properties['sellModifier'])
			? (double)$properties['sellModifier'] : 0.00;

		// Get the properties that will be applied to single items.
		$property = [
			'buyPrice'  => $this->request->input('buyPrice' ) ?: 0.00,
			'sellPrice' => $this->request->input('sellPrice') ?: 0.00,
		];

		$property['buyPrice'] = is_numeric($property['buyPrice'])
			? (double)$property['buyPrice' ] : 0.00;

		$property['sellPrice'] = is_numeric($property['sellPrice'])
			? (double)$property['sellPrice'] : 0.00;

		// Get the items being updated.
		$ids   = explode(',', $this->request->input('items'));
		$ids   = count($ids) && $ids[0] != '' ? $ids : [];
		$items = $this->item_model->whereIn('typeID', $ids)->get();

		try {
			if ($items->count() == 0) { throw new \Exception(''); }

			DB::transaction(function () use ($items, $properties, $property) {
				// Update a single item.
				if ($items->count() == 1) {
					$items[0]->update(array_merge($properties, $property));

				// Update multiple items.
				} else if ($items->count() > 1) {
					$items->each(function ($item) use ($properties) {
						$item->update($properties);
					});

				} else {
					return $this->ajaxFailureResponse(
						trans('buyback.messages.edit_items_nothing', $items->count()));
				}
			});

			return $this->ajaxSuccessResponse(
				trans_choice('buyback.messages.edit_items_success', $items->count() == 1 ? 1 : 2));

		} catch (\Exception $e) {
			return $this->ajaxFailureResponse(
				trans_choice('buyback.messages.edit_items_failure', $items->count() == 1 ? 1 : 2));
		}
	}

	/**
	 * Handles removing existing buyback items.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function postRemoveItems()
	{
		if (!$this->request->ajax()) { return $this->ajaxErrorResponse(); }

		$ids   = explode(',', $this->request->input('types'));
		$ids   = count($ids) && $ids[0] != '' ? $ids : [];
		$items = $this->item_model->whereIn('typeID', $ids)->get();

		try {
			if ($items->count() == 0) {
				return $this->ajaxFailureResponse(
					trans('buyback.messages.remove_items_nothing', $items->count()));
			}

			$items->each(function ($item) { $item->delete(); });

			return $this->ajaxSuccessResponse(
				trans_choice('buyback.messages.remove_items_success', $items->count() == 1 ? 1 : 2));

		} catch (\Exception $e) {
			return $this->ajaxFailureResponse(
				trans_choice('buyback.messages.remove_items_failure', $items->count() == 1 ? 1 : 2));
		}
	}

	/**
	 * Handles dispatching the update item prices job.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function postUpdateItems()
	{
		if (!$this->request->ajax()) { return $this->ajaxErrorResponse(); }

		try {
			$this->dispatchNow($this->update_items_job);

			return $this->ajaxSuccessResponse(
				trans('buyback.messages.update_item_prices_success'));

		} catch (\Exception $e) {
			return $this->ajaxFailureResponse(
				trans('buyback.messages.update_item_prices_failure'));
		}
	}
}
