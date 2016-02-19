<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Pheal\Pheal;

class UpdateAPI extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle(Pheal $pheal)
	{
		try {
			$apiKeyInfo = $pheal->accountScope->ApiKeyInfo();
			$accessMask = $apiKeyInfo->key->accessMask;
			$accessType = $apiKeyInfo->key->type;
			$scope      = substr(lcfirst($accessType), 0, 4).'Scope';

			$contracts  = $pheal->$scope->Contracts();

			foreach ($contracts as $contract) {
				//
			}

		} catch (Exception $e) {
			//
		}
	}
}
