<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Pheal\Access\StaticCheck;
use Pheal\Cache\PredisStorage;
use Pheal\Core\Config;
use Pheal\Log\FileStorage;
use Pheal\Pheal;

class PhealServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Config::getInstance()->cache = new PredisStorage([
			'host'       => '127.0.0.1',
			'port'       => 6379,
			'persistent' => true,
			'password'   => null,
			'prefix'     => 'Pheal'
		]);

		Config::getInstance()->log   = new FileStorage(
			storage_path() . '/logs/',
			[
				'access_log'      => 'pheal_access.log',
				'error_log'       => 'pheal_error.log',
				'access_format'   => "%s [%s] %2.4fs %s\n",
				'error_format'    => "%s [%s] %2.4fs %s \"%s\"\n",
				'truncate_apikey' => true,
				'umask'           => 0666,
				'umask_directory' => 0777,
			]
		);

		Config::getInstance()->access = new StaticCheck();
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(Pheal::class, function ($app) {
			return new Pheal(config('services.eveapi.keyid'), config('services.eveapi.vcode'));
		});
	}
}
