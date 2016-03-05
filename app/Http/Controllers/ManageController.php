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
		$models = $this->contract
			->where('status', 'Outstanding')
			->orderBy('contractID', 'DESC')
			->get();

		$buying        = [];
		$selling       = [];
		$buyback_items = $this->item->with('type')->get();

		foreach ($models as $contract) {
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
			dd($buyback);





			// Return a cached result if possible.
			$this->cache->forget("contract:{$contract->contractID}");

			if ($this->cache->has("contract:{$contract->contractID}")) {
				if ($contract->reward == 0) {
					$buying[]  = $this->cache->get("contract:{$contract->contractID}");
				} else {
					$selling[] = $this->cache->get("contract:{$contract->contractID}");
				} continue;
			}

			// Calculate the buyback.
			$items   = $this->parser->convertContractToItems($contract);
			$buyback = $this->refinery->calculateBuyback($items);

			// Insert needed items into the buyback object.
			$buyback->contract   = $contract;
			$buyback->price      = $contract->price;
			$buyback->station    = $this->helper->convertStationIdToModel ($contract->startStationID);
			$buyback->issuerName = $this->helper->convertCharacterIdToName($contract->issuerID      );

			// Insert the profit margin.
			if ($contract->price > 0 && $buyback->totalValue > 0) {
				$buyback->margin = 100 - ($contract->price / $buyback->totalValue * 100);
			} else { $buyback->margin = 0; }

			// Insert the buyback item model into raw items and materials.
			foreach ($buyback->materials as $typeID => &$material) {
				$buyback_item = $buyback_items->where('typeID', $typeID)->first();

				$material = (object)[
					'buybackItem' => $buyback_item,
					'type'        => $buyback_item->type,
					'quantity'    => $material,
				];
			}

			// Convert numbers into a easier to read format.
			$buyback->price       = $buyback->price       == 0 ? $buyback->price       : $this->helper->thousandsCurrencyFormat($buyback->price      );
			$buyback->totalValue  = $buyback->totalValue  == 0 ? $buyback->totalValue  : $this->helper->thousandsCurrencyFormat($buyback->totalValue );
			$buyback->totalModded = $buyback->totalModded == 0 ? $buyback->totalModded : $this->helper->thousandsCurrencyFormat($buyback->totalModded);
			$buyback->totalProfit = $buyback->totalProfit == 0 ? $buyback->totalProfit : $this->helper->thousandsCurrencyFormat($buyback->totalProfit);

			// Cache and add the results to the objects that will be added to the view.
			$this->cache->put("contract:{$contract->contractID}", $buyback, $this->carbon->now()->addMinutes(30));

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
