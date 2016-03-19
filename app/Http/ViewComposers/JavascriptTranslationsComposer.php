<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class JavascriptTranslationsComposer
{

	/**
	 * Constructs the class.
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Binds data to the view.
	 * @param  View $view
	 * @return void
	 */
	public function compose(View $view)
	{
		$view->with('trans', json_encode(trans('buyback')));
	}
}
