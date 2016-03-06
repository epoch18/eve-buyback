<?php

namespace App\Http\Controllers;

use App\EveOnline\Helper;
use App\EveOnline\Parser;
use App\EveOnline\Refinery;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\API\Contract;
use App\Models\API\Outpost;
use App\Models\SDE\StaStation;
use App\Models\SDE\InvType;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Http\Request;

class ManageController extends Controller
{
	/**
	* @var \Illuminate\Cache\Repository
	*/
	private $cache;

	/**
	* @var \Carbon\Carbon
	*/
	private $carbon;

	/**
	* @var \App\Models\API\Contract
	*/
	private $contract;

	/**
	* @var \App\Models\API\Outpost
	*/
	private $outpost;

	/**
	* @var \App\Models\SDE\StaStation
	*/
	private $station;

	/**
	* @var \App\Models\InvType
	*/
	private $type;

	/**
	* @var \App\Models\Item
	*/
	private $item;

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
	* @var \Illuminate\Http\Request
	*/
	private $request;

	/**
	* Constructs the class.
	* @param  \Illuminate\Cache\Repository $cache
	* @param  \Carbon\Carbon               $carbon
	* @param  \App\Models\API\Contract     $contract
	* @param  \App\Models\API\Outpost      $outpost
	* @param  \App\Models\SDE\StaStation   $station
	* @param  \App\Models\SDE\InvType      $type
	* @param  \App\Models\Item             $item
	* @param  \App\EveOnline\Parser        $parser
	* @param  \App\EveOnline\Refinery      $refinery
	* @param  \Illuminate\Http\Request     $request
	*/
	public function __construct(
		Cache      $cache,
		Carbon     $carbon,
		Contract   $contract,
		Outpost    $outpost,
		StaStation $station,
		InvType    $type,
		Item       $item,
		Helper     $helper,
		Parser     $parser,
		Refinery   $refinery,
		Request    $request
	) {
		$this->cache    = $cache;
		$this->carbon   = $carbon;
		$this->contract = $contract;
		$this->outpost  = $outpost;
		$this->station  = $station;
		$this->type     = $type;
		$this->item     = $item;
		$this->helper   = $helper;
		$this->parser   = $parser;
		$this->refinery = $refinery;
		$this->request  = $request;
	}

	/**
	 * Handles displaying contracts assigned to the character or corporation.
	 */
	public function contract()
	{
		$contracts = $this->contract
			->where('status', 'Outstanding')
			->orderBy('contractID', 'DESC')
			->get();

		$buying        = [];
		$selling       = [];
		$buyback_items = $this->item->with('type')->get();

		foreach ($contracts as $contract) {
			// Calculate the buyback.
			$items   = $this->parser->convertContractToItems($contract);
			$buyback = $this->refinery->calculateBuyback($items);

			// Insert the profit margin.
			if ($contract->price > 0 && $buyback->totalValue > 0) {
				$buyback->totalMargin = 100 - ($contract->price / $buyback->totalValue * 100);
			} else { $buyback->totalMargin = 0; }

			// Insert convenience items into the buyback object.
			$buyback->contract        = $contract;
			$buyback->contractPrice   = $contract->price;
			$buyback->contractStation = $this->helper->convertStationIdToModel ($contract->startStationID);
			$buyback->contractIssuer  = $this->helper->convertCharacterIdToName($contract->issuerID      );

			if ($contract->reward == 0) {
				$buying[]  = $buyback;
			} else {
				$selling[] = $buyback;
			}

			continue;
		}

		return view('manage.contract')
			->withBuying ($buying )
			->withSelling($selling);
	}
}
