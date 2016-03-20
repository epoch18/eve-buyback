<?php

namespace App\Http\ViewComposers;

use App\Models\Setting;
use Illuminate\View\View;

class BuybackOwnerComposer
{
	/**
	 * @var  \App\Models\Setting
	 */
	private $setting_model;

	/**
	 * Constructs the class.
	 * @return void
	 */
	public function __construct(Setting $setting_model)
	{
		$this->setting_model = $setting_model;
	}

	/**
	 * Binds data to the view.
	 * @param  View $view
	 * @return void
	 */
	public function compose(View $view)
	{
		$owner = $this->setting_model->where('key', 'LIKE', 'owner%')->get();

		$ownerID   = $owner->where('key', 'ownerID'  )->first();
		$ownerName = $owner->where('key', 'ownerName')->first();
		$ownerType = $owner->where('key', 'ownerType')->first();

		$ownerID   = $ownerID   ? $ownerID  ->value : '';
		$ownerName = $ownerName ? $ownerName->value : '';
		$ownerType = $ownerType ? $ownerType->value : '';

		$ownerLink = $ownerType && $ownerType == 'Character'
			? '<a href="#" onclick="CCPEVE.showInfo(1377, '.$ownerID.')">'.$ownerName.'</a>'
			: '<a href="#" onclick="CCPEVE.showInfo(2, '   .$ownerID.')">'.$ownerName.'</a>';

		$view->with('ownerID'  , $ownerID  );
		$view->with('ownerName', $ownerName);
		$view->with('ownerType', $ownerType);
		$view->with('ownerLink', $ownerLink);
	}
}
