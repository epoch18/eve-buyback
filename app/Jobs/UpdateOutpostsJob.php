<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\API\Outpost;
use DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Pheal\Pheal;

class UpdateOutpostsJob extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	/**
	 * @var \App\Models\API\Outpost;
	 */
	private $outpost;

	/**
	 * @var \Pheal\Pheal
	 */
	private $pheal;

	/**
	 * Create a new job instance.
	 * @param  \App\Models\API\Outpost $outpost
	 * @param  \Pheal\Pheal            $pheal
	 * @return void
	 */
	public function __construct(Outpost $outpost, Pheal $pheal)
	{
		$this->outpost = $outpost;
		$this->pheal   = $pheal;
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		try {
			Log::info('Updating outposts.');

			DB::transaction(function () {
				$stations = $this->pheal->eveScope->ConquerableStationList();

				foreach ($stations->outposts as $station) {
					$this->outpost->updateOrCreate([
						'stationID' => $station->stationID,
					], [
						'stationName'     => $station->stationName,
						'stationTypeID'   => $station->stationTypeID,
						'solarSystemID'   => $station->solarSystemID,
						'corporationID'   => $station->corporationID,
						'corporationName' => $station->corporationName,
						'x'               => $station->x,
						'y'               => $station->y,
						'z'               => $station->z,
					]);
				}

			}); // transaction

		} catch (Exception $e) {
			Log::error('Failed updating outposts. Throwing exception:');
			throw $e;
		}
	}
}
