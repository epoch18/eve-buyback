<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\EveOnline\Helper;
use App\EveOnline\Parser;
use App\EveOnline\Refinery;
use App\Models\API\Contract;
use App\Models\Item;

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
	* @var \App\Models\API\Contract
	*/
	private $contract_model;

	/**
	* @var \App\Models\Item
	*/
	private $item_model;

	/**
	 * Constructs the class.
	 * @param  \App\EveOnline\Helper    $helper
	 * @param  \App\EveOnline\Parser    $parser
	 * @param  \App\EveOnline\Refinery  $refinery
	 * @param  \App\Models\API\Contract $contract_model
	 * @param  \App\Models\Item         $item_model
	 */
	public function __construct(
		Helper   $helper,
		Parser   $parser,
		Refinery $refinery,
		Contract $contract_model,
		Item     $item_model
	) {
		$this->helper         = $helper;
		$this->parser         = $parser;
		$this->refinery       = $refinery;
		$this->contract_model = $contract_model;
		$this->item_model     = $item_model;
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
}
