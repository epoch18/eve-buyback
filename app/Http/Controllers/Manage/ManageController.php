<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Setting;

class ManageController extends Controller
{
	/**
	* @var \App\Models\Item
	*/
	private $item_model;

	/**
	* @var \App\Models\Setting
	*/
	private $setting_model;

	/**
	* Constructs the class.
	* @param  \App\Models\Item    $item_model
	* @param  \App\Models\Setting $setting_model
	*/
	public function __construct(Item $item_model, Setting $setting_model)
	{
		$this->item_model    = $item_model;
		$this->setting_model = $setting_model;
	}

	/**
	 * Handles displaying the management page.
	 * @return \Illuminate\Http\Response
	 */
	public function getIndex()
	{
		$motd = $this->setting_model->where('key', 'motd')->first();
		$motd = $motd ? $motd->value : '';

		$items = $this->item_model
			->with('type')
			->with('type.group')
			->with('type.group.category')
			->get();

		return view('manage.index')
			->withMotd ($motd )
			->withItems($items);
	}
}
