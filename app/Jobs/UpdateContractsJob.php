<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\API\Contract;
use App\Models\API\ContractItem;
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
	private $contract;

	/**
	 * @var \App\Models\API\ContractItem;
	 */
	private $contract_item;

	/**
	 * @var \Pheal\Pheal
	 */
	private $pheal;

	/**
	 * Create a new job instance.
	 * @param  \App\Models\API\Contract     $contract
	 * @param  \App\Models\API\ContractItem $contract_item
	 * @param  \Pheal\Pheal                 $pheal
	 * @return void
	 */
	public function __construct(Contract $contract, ContractItem $contract_item, Pheal $pheal)
	{
		$this->contract      = $contract;
		$this->contract_item = $contract_item;
		$this->pheal         = $pheal;
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		try {
			Log::info('Updating api records.');

			$apiKeyInfo = $this->pheal->accountScope->ApiKeyInfo();
			$accessMask = $apiKeyInfo->key->accessMask;
			$accessType = $apiKeyInfo->key->type;
			$scope      = substr(lcfirst($accessType), 0, 4).'Scope';

			// Update contracts and contract items.
			DB::transaction(function () use ($scope) {
				Log::info('Updating contracts and contract items.');

				$contracts = $this->pheal->$scope->Contracts();

				foreach ($contracts->contractList as $contract) {
					$this->contract->updateOrCreate([
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
					if (!!$this->contract_item->where('contractID', $contract->contractID)->first()) {
						continue;
					}

					$items = $this->pheal->$scope->ContractItems(['contractID' => $contract->contractID]);

					foreach ($items->itemList as $item) {
						$this->contract_item->updateOrCreate([
							'recordID'     => $item->recordID,
						], [
							'contractID'  => $contract->contractID,
							'typeID'      => $item->typeID,
							'quantity'    => $item->quantity,
							'rawQuantity' => isset($item->rawQuantity) ? $item->rawQuantity : 0,
							'singleton'   => $item->singleton,
							'included'    => $item->included,
						]);
					} // items
				} // contracts
			}); // transaction

		} catch (Exception $e) {
			Log::error('Failed updating api records. Throwing exception:');
			throw $e;
		}
	}
}
