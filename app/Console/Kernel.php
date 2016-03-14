<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		\App\Console\Commands\UpdateContractsCommand::class,
		\App\Console\Commands\UpdateItemsCommand::class,
		\App\Console\Commands\UpdateOutpostsCommand::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule	$schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('buyback:update-contracts')->hourly();
		$schedule->command('buyback:update-items'    )->hourly();
		$schedule->command('buyback:update-outposts' )->hourly();
	}
}
