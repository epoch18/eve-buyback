<?php

namespace App\EveOnline;

use App\Models\API\Outpost;
use App\Models\SDE\StaStation;
use Carbon\Carbon;
use Illuminate\Cache\Repository as Cache;
use Pheal\Pheal;

/**
 * Contains helper functions that don't have any other home.
 */
class Helper
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
	* @var \App\Models\API\Outpost
	*/
	private $outpost;

	/**
	* @var \App\Models\SDE\StaStation
	*/
	private $station;

	/**
	* @var \Pheal\Pheal
	*/
	private $pheal;

	/**
	* Constructs the class.
	* @param  \Illuminate\Cache\Repository $cache
	* @param  \Carbon\Carbon               $carbon
	* @param  \App\Models\API\Outpost      $outpost
	* @param  \App\Models\SDE\StaStation   $station
	* @param  \Pheal\Pheal                 $pheal
	*/
	public function __construct(
		Cache      $cache,
		Carbon     $carbon,
		Outpost    $outpost,
		StaStation $station,
		Pheal      $pheal
	) {
		$this->cache    = $cache;
		$this->carbon   = $carbon;
		$this->outpost  = $outpost;
		$this->station  = $station;
		$this->pheal    = $pheal;
	}

	public function convertStationIdToName($id)
	{
		if ($this->cache->has("station:{$id}")) {
			return $this->cache->get("station:{$id}");
		}

		if ($station = $this->station->where('stationID', $id)->first()) {
			$result = $station->stationName;

		} else if ($station = $this->outpost->where('stationID', $id)->first()) {
			$result = $station->stationName;

		} else {
			$result = 'Unknown Station';
		}

		$this->cache->put("station:{$id}", $result, $this->carbon->now()->addHours(24));
		return $result;
	}

	public function convertCharacterIdToName($id)
	{
		if ($this->cache->has("character:{$id}")) {
			return $this->cache->get("character:{$id}");
		}

		$response = $this->pheal->eveScope->CharacterName(['ids' => $id]);
		$result   = 'Unknown Character';

		if (count($response->characters) == 1) {
			$result = $response->characters[0]->name;
			$this->cache->put("character:{$id}", $result, $this->carbon->now()->addHours(24));
		}

		return $result;
	}
}
