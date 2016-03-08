<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomeControllerTest extends TestCase
{
	use DatabaseMigrations;

	public function testViewIndex()
	{
		\App\Models\Item::create([
			'typeID'         => 34,
			'typeName'       => 'Tritanium',
			'buyRaw'         => true,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 0.9,
			'buyPrice'       => 5.87,
			'sell'           => true,
			'sellModifier'   => 1.0,
			'sellPrice'      => 6.50,
			'lockPrices'     => false,
		]);

		\App\Models\Item::create([
			'typeID'         => 35,
			'typeName'       => 'Pyerite',
			'buyRaw'         => true,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 0.9,
			'buyPrice'       => 10.60,
			'sell'           => true,
			'sellModifier'   => 1.0,
			'sellPrice'      => 13.28,
			'lockPrices'     => false,
		]);

		$this->visit('/')
			->see('Tritanium')->see('5.28')->see('6.50' )
			->see('Pyerite'  )->see('9.54')->see('13.28')
		;
	}

	public function testPasteIndex()
	{
		\App\Models\Item::create([
			'typeID'         => 34,
			'typeName'       => 'Tritanium',
			'buyRaw'         => true,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 0.9,
			'buyPrice'       => 5.87,
			'sell'           => true,
			'sellModifier'   => 1.0,
			'sellPrice'      => 6.50,
			'lockPrices'     => false,
		]);

		$this->visit('/')
			->type("Tritanium\t10\tMineral\tMaterial\n", 'pasteData')
			->press('pasteSubmit')
			->see(ucfirst(trans('buyback.acceptable')))->see('Tritanium')->see('10')->see('52.83')
		;

		$this->visit('/')
			->type("Tritanium\t10\tMineral\tMaterial\nPyerite\t10\tMineral\tMaterial", 'pasteData')
			->press('pasteSubmit')
			->see(ucfirst(trans('buyback.unwanted')))->see('Pyerite')->see('10')
		;
	}
}
