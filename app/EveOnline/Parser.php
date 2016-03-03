<?php

namespace App\EveOnline;

use App\Models\SDE\InvType;

/**
 * Handles converting text into usable objects.
 */
class Parser
{
	/**
	* @var \App\Models\SDE\InvType
	*/
	private $type;

	/**
	 * Constructs the class.
	 * @param InvType $type
	 */
	public function __construct(InvType $type)
	{
		$this->type = $type;
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

		// Split text on newlines.
		if (!preg_match_all('/(.*?)\t(.*?)\t.*?(?:\r\n|\r|\n|$)/', $text, $rows)) {
			return false;
		}

		for ($i = 0; $i < count($rows[0]); $i++) {
			// Get the neccessary fields and strip any currency separators.
			$separators = [' ', ',', '.'];
			$name       = $rows[1][$i];
			$qty        = str_replace($separators, '', $rows[2][$i]);

			// Don't continue if name or qty are the wrong types.
			if (!is_string($name) || !is_numeric($qty)) {
				continue;
			}

			// Find the item in the database.
			$type = $this->type->with('materials')->where('typeName', $name)->first();
			if (!$type) {
				continue;
			}

			// Add the item model and quantity to the returned results.
			$result[] = (object)[
				'type'     => $type,
				'quantity' => (integer)$qty,
			];

			continue;
		}

		// Return as an object to avoid mixed array/object formating.
		return $result;
	}
}
