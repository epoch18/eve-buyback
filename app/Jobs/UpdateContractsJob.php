<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\API\Contract;
use App\Models\API\ContractItem;
use App\Models\Setting;
use DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Pheal\Pheal;

class UpdateContractsJob extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	/**
	 * @var \App\Models\API\Contract;
	 */
	private $contract_model;

	/**
	 * @var \App\Models\API\ContractItem;
	 */
	private $contract_item_model;

	/**
	 * @var \App\Models\Setting;
	 */
	private $setting_model;

	/**
	 * @var \Pheal\Pheal
	 */
	private $pheal;

	/**
	 * Create a new job instance.
	 * @param  \App\Models\API\Contract     $contract
	 * @param  \App\Models\API\ContractItem $contract_item
	 * @param  \App\Models\Setting          $setting_model
	 * @param  \Pheal\Pheal                 $pheal
	 * @return void
	 */
	public function __construct(
		Contract     $contract_model,
		ContractItem $contract_item_model,
		Setting      $setting_model,
		Pheal        $pheal
	) {
		$this->contract_model      = $contract_model;
		$this->contract_item_model = $contract_item_model;
		$this->setting_model       = $setting_model;
		$this->pheal               = $pheal;
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		try {
			Log::info('Updating contracts.');

			$apiKeyInfo = $this->pheal->accountScope->ApiKeyInfo();
			$accessMask = $apiKeyInfo->key->accessMask;
			$accessType = $apiKeyInfo->key->type;
			$scope      = substr(lcfirst($accessType), 0, 4).'Scope';

			if ($accessType == 'Account') {
				Log::error('Failed updating contracts. The api key must be a character or corporation.');
				return;
			}

			Log::info('Updating contract owner details.');

			$this->setting_model->updateOrCreate(
				['key'   => 'ownerID'],
				['value' => $scope == 'char'
					? $apiKeyInfo->key->characters[0]->characterID
					: $apiKeyInfo->key->characters[0]->corporationID
				]
			);

			$this->setting_model->updateOrCreate(
				['key'   => 'ownerName'],
				['value' => $accessType == 'Character'
					? $apiKeyInfo->key->characters[0]->characterName
					: $apiKeyInfo->key->characters[0]->corporationName
				]
			);

			$this->setting_model->updateOrCreate(
				['key'   => 'ownerType'],
				['value' => $accessType]
			);

			// Update contracts and contract items.
			DB::transaction(function () use ($scope) {
				Log::info('Updating contracts and contract items.');

				$contracts = $this->pheal->$scope->Contracts();

				foreach ($contracts->contractList as $contract) {
					$this->contract_model->updateOrCreate([
						'contractID'     => $contract->contractID,
					], [
						'issuerID'       => $contract->issuerID,
						'issuerCorpID'   => $contract->issuerCorpID,
						'assigneeID'     => $contract->assigneeID,
						'acceptorID'     => $contract->acceptorID,
						'startStationID' => $contract->startStationID,
						'endStationID'   => $contract->endStationID,
						'type'           => $contract->type,
						'status'         => $contract->status,
						'title'          => $contract->title,
						'forCorp'        => $contract->forCorp,
						'availability'   => $contract->availability,
						'dateIssued'     => $contract->dateIssued,
						'dateExpired'    => $contract->dateExpired,
						'dateAccepted'   => $contract->dateAccepted,
						'numDays'        => $contract->numDays,
						'dateCompleted'  => $contract->dateCompleted,
						'price'          => $contract->price,
						'reward'         => $contract->reward,
						'collateral'     => $contract->collateral,
						'buyout'         => $contract->buyout,
						'volume'         => $contract->volume,
					]);

					// Do not fetch contract items if they already exist.
					if (!!$this->contract_item_model->where('contractID', $contract->contractID)->first()) {
						continue;
					}

					try {
                        $items = $this->pheal->$scope->ContractItems(['contractID' => $contract->contractID]);

                        foreach ($items->itemList as $item) {
                            $this->contract_item_model->updateOrCreate([
                                'recordID' => $item->recordID,
                            ], [
                                'contractID' => $contract->contractID,
                                'typeID' => $item->typeID,
                                'quantity' => $item->quantity,
                                'rawQuantity' => isset($item->rawQuantity) ? $item->rawQuantity : 0,
                                'singleton' => $item->singleton,
                                'included' => $item->included,
                            ]);
                        } // items
                    } catch (\Pheal\Exceptions\ConnectionException $connectionException) {
                        logger()->warning('Unable to get Items for a contract', ['contractID' => $contract->contractID, 'contractData' => $contract]);
                    }
				} // contracts
			}); // transaction

		} catch (Exception $e) {
			Log::error('Failed updating contracts. Throwing exception:');
			throw $e;
		}
	}
}
