<?php

namespace App\Providers;

use App\Http\ViewComposers\BuybackOwnerComposer;
use App\Http\ViewComposers\JavascriptTranslationsComposer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		view()->composer('*', BuybackOwnerComposer::class);
		view()->composer('*', JavascriptTranslationsComposer::class);
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
