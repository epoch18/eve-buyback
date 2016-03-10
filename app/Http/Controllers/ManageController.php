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
use App\Models\Setting;
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
	* @var \App\Models\Setting
	*/
	private $setting;

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
	* @param  \App\Models\Setting          $setting
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
		Request    $request,
		Setting    $setting
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
		$this->setting  = $setting;
	}

	/**
	 * Handles displaying contracts assigned to the character or corporation.
	 */
	public function contract()
	{
		$buyback_items = $this->item->with('type')->get();
		$contracts     = $this->contract
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

		return view('manage.contract')
			->withBuying ($buying )
			->withSelling($selling);
	}

	public function config()
	{
		$motd  = $this->setting->where('key', 'motd')->first();
		$motd  = $motd ? $motd->value : '';

		$items = $this->item
			->with('type')
			->with('type.group')
			->with('type.group.category')
			->get();

		return view('manage.config')
			->withMotd ($motd )
			->withItems($items);
	}

	public function motd()
	{
		if (!$this->request->ajax()) {
			return response()->json(['result' => false]);
		}

		$text = strip_tags($this->request->input('text')) ?: '';

		if (strlen($text) == 0) {
			$this->setting->where('key', 'motd')->delete();

			return response()->json([
				'result'  => true,
				'message' => trans('buyback.config.motd.removed'),
			]);
		}

		if (strlen($text) > 5000) {
			return response()->json([
				'result'  => false,
				'message' => trans('validation.max.string',
					['attribute' => 'text', 'max' => 5000]),
			]);
		}

		$this->setting->updateOrCreate(
			['key'   => 'motd'],
			['value' => $text ]
		);

		return response()->json([
			'result' => true,
			'message' => trans('buyback.config.motd.updated'),
		]);
	}

	public function getItems()
	{
		return $this->item
			->with('type')
			->with('type.group')
			->with('type.group.category')
			->get()
			->toJson();
	}

	public function items()
	{
		if (!$this->request->ajax()) {
			return response()->json(['result' => false]);
		}

		return response()->json([
			'result' => true,
			'message' => trans('buyback.config.items.updated'),
		]);
	}
}
