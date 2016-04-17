<?php

namespace App\Http\Controllers\Contract;

use App\EveOnline\Helper;
use App\EveOnline\Parser;
use App\EveOnline\Refinery;
use App\Http\Controllers\Controller;
use App\Jobs\UpdateContractsJob;
use App\Models\API\Contract;
use App\Models\Item;
use Illuminate\Bus\Dispatcher;

class ContractController extends Controller
{
	/**
	 * @var \App\EveOnline\Helper
	 */

	private $helper;
	/**
	 * @var \App\EveOnline\Parser
	 */
	private $parser;

	/**
	 * @var \App\EveOnline\Refinery
	 */
	private $refinery;

	/**
	 * @var \App\Jobs\UpdateContractsJob
	 */
	private $update_contracts_job;

	/**
	 * @var \App\Models\API\Contract
	 */
	private $contract_model;

	/**
	 * @var \App\Models\Item
	 */
	private $item_model;

	/**
	 * @var \Illuminate\Bus\Dispatcher
	 */
	private $dispatcher;

	/**
	 * Constructs the class.
	 * @param  \App\EveOnline\Helper        $helper
	 * @param  \App\EveOnline\Parser        $parser
	 * @param  \App\EveOnline\Refinery      $refinery
	 * @param  \App\Jobs\UpdateContractsJob $update_contracts_job
	 * @param  \App\Models\API\Contract     $contract_model
	 * @param  \App\Models\Item             $item_model
	 * @param  \Illuminate\Bus\Dispatcher   $dispatcher
	 */
	public function __construct(
		Helper             $helper,
		Parser             $parser,
		Refinery           $refinery,
		UpdateContractsJob $update_contracts_job,
		Contract           $contract_model,
		Item               $item_model,
		Dispatcher         $dispatcher
	) {
		$this->helper               = $helper;
		$this->parser               = $parser;
		$this->refinery             = $refinery;
		$this->update_contracts_job = $update_contracts_job;
		$this->contract_model       = $contract_model;
		$this->item_model           = $item_model;
		$this->dispatcher           = $dispatcher;
	}

	/**
	 * Handles displaying contracts that are assigned to the character or corporation.
	 * @return \Illuminate\Http\Response
	 */
	public function getIndex()
	{
		$buyback_items = $this->item_model->with('type')->get();

		$contracts = $this->contract_model
			->with('items')
			->with('items.type')
			->with('items.type.group')
			->with('items.type.group.category')
			->with('items.type.materials')
			->where('status', 'Outstanding')
			->orderBy('contractID', 'DESC')
			->get();

		$buying = $selling = [];

		foreach ($contracts as $contract) {
			$items = $this->parser->convertContractToItems($contract);

			if ($contract->price > 0) {
				$buying[] = $buyback = $this->refinery->calculateBuyback($items, $buyback_items);

				// Insert the profit margin.
				$buyback->totalMargin = 0;

				if ($contract->price > 0 && $buyback->totalValue > 0) {
					$buyback->totalMargin = 100 - ($contract->price / $buyback->totalValue * 100);
				}

				// Insert convenience items into the buyback object.
				$buyback->contract        = $contract;
				$buyback->contractPrice   = $contract->price;
				$buyback->contractStation = $this->helper->convertStationIdToModel ($contract->startStationID);
				$buyback->contractIssuer  = $this->helper->convertCharacterIdToName($contract->issuerID      );

			} else if ($contract->reward > 0) {
			}
		}

		return view('contract.index')
			->withBuying ($buying )
			->withSelling($selling);
	}

	public function postUpdateContracts()
	{
		try {
			$this->dispatcher->dispatchNow($this->update_contracts_job);

			return $this->ajaxSuccessResponse(
				trans('buyback.messages.update_contracts_success'));

		} catch (\Exception $e) {
			return $this->ajaxFailureResponse(
				trans('buyback.messages.update_contracts_failure'));
		}
	}
}
