<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class MotdController extends Controller {
	/**
	* @var \Illuminate\Http\Request
	*/
	private $request;

	/**
	* @var \App\Models\Setting
	*/
	private $setting_model;

	/**
	* Constructs the class.
	* @param \Illuminate\Http\Request $request
	* @param \App\Models\Setting      $setting_model
	*/
	public function __construct(Request $request, Setting $setting_model)
	{
		$this->request       = $request;
		$this->setting_model = $setting_model;
	}

	public function postEditMotd()
	{
		if (!$this->request->ajax()) { return $this->ajaxErrorResponse(); }

		// Strip any bad tags.
		$text = htmlentities(strip_tags($this->request->input('text'))) ?: '';

		// Remove the motd if no text was entered.
		if (strlen($text) == 0) {
			$this->setting_model->where('key', 'motd')->delete();

			return $this->ajaxSuccessResponse(
				trans('buyback.messages.motd_removed'));
		}

		// Validate the text.
		if (strlen($text) > 5000) {
			return $this->ajaxFailureResponse(
				trans('validation.max.string',
					['attribute' => 'text', 'max' => 5000]));
		}

		// Attempt to edit the motd.
		try {
			$this->setting_model->updateOrCreate(
				['key'   => 'motd'],
				['value' => $text ]
			);

			return $this->ajaxSuccessResponse(
				trans('buyback.messages.motd_edited'));

		} catch (\Exception $e) {
			return $this->ajaxFailureResponse(
				trans('buyback.message.motd_failure'));
		}
	}
}
