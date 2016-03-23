<?php

namespace App\EveOnline;

use App\Models\API\Contract;
use App\Models\API\ContractItem;
use App\Models\SDE\InvType;
use Carbon\Carbon;
use Illuminate\Cache\Repository as Cache;

/**
 * Handles converting text into usable objects.
 */
class Parser
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
	* @var \App\Models\SDE\InvType
	*/
	private $type;

	/**
	 * Constructs the class.
	* @param  \Illuminate\Cache\Repository $cache
	* @param  \Carbon\Carbon               $carbon
	* @param  \App\Models\SDE\InvType      $type
	 * @param InvType $type
	 */
	public function __construct(Cache $cache, Carbon $carbon, InvType $type)
	{
		$this->cache  = $cache;
		$this->carbon = $carbon;
		$this->type   = $type;
	}

	/**
	 * Converts text copied from the list or detail view in eve into
	 * an array of items and quantities.
	 * @param  string $text
	 * @return array
	 */
	public function convertTextToItems($text)
	{
		$result = [];

		// Split text on newlines and capture the item name and quantity.
		// \xC2\xA0 are non-breaking spaces present in some locales.
		if (!preg_match_all('/(.*?)\t([\d,. \xC2\xA0]*).*(?:\r\n|\r|\n|$)/', $text, $rows)) {
			return false;
		}

		for ($i = 0; $i < count($rows[0]); $i++) {
			// Get the neccessary fields and strip any currency separators.
			$name = $rows[1][$i];
			$qty  = preg_replace('/[\D]/', '', $rows[2][$i]);

			// Don't continue if name or qty are the wrong types.
			if (!is_string($name) || !is_numeric($qty)) { continue; }

			// Find the type in the cache.
			if ($this->cache->has("type:{$name}")) {
				$type = $this->cache->get("type:{$name}");

				$result[] = (object)[
					'type'     => $type,
					'quantity' => (integer)$qty,
				]; continue;
			}

			// Find the type in the database and cache it.
			$type = $this->type
				->with('materials')
				->with('group')
				->with('group.category')
				->where('typeName', $name)
				->first();

			if (!$type) { continue; }

			$this->cache->forever("type:{$name}", $type);

			$result[] = (object)[
				'type'     => $type,
				'quantity' => (integer)$qty,
			]; continue;
		}

		return $result;
	}

	/**
	 * Converts a contract into an array of items and quantities.
	 * @param  App\Models\API\Contract $contract
	 * @return array
	 */
	public function convertContractToItems(Contract $contract)
	{
		$result = [];

		foreach ($contract->items as $item) {
			$result[] = (object)[
				'type'     => $item->type,
				'quantity' => (integer)$item->quantity,
			];

			continue;
		}

		return $result;
	}
}
