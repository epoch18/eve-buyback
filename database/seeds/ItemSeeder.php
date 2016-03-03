<?php

use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		foreach ([34, 35, 36, 37, 38, 39, 40, 11399] as $typeID) {
			\App\Models\Item::updateOrCreate(
				['typeID' => $typeID],
				[
					'buyRaw'       => true,
					'buyRecycled'  => true,
					'buyRefined'   => true,
					'buyModifier'  => 0.90,
					'buyPrice'     => 1.00,
					'sell'         => true,
					'sellModifier' => 1.00,
					'sellPrice'    => 1.00,
					'lockPrices'   => false,
				]
			);
		}
	}
}
