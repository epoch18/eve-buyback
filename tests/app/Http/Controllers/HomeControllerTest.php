<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomeControllerTest extends TestCase
{
	use DatabaseMigrations;

	private $headers;

	public function setUp()
	{
		parent::setUp();

		$this->headers = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];
	}

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
			->see(ucfirst(trans('buyback.headers.acceptable')))->see('Tritanium')->see('10')->see('52.83')
		;

		$this->visit('/')
			->type("Tritanium\t10\tMineral\tMaterial\nPyerite\t10\tMineral\tMaterial", 'pasteData')
			->press('pasteSubmit')
			->see(ucfirst(trans('buyback.headers.unwanted')))->see('Pyerite')->see('10')
		;
	}

	public function testGetAsteroids()
	{
		\App\Models\Item::create([
			'typeID'         => 34,
			'typeName'       => 'Tritanium',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => true,
			'buyModifier'    => 0.9,
			'buyPrice'       => 5.87,
			'sell'           => true,
			'sellModifier'   => 1.0,
			'sellPrice'      => 6.50,
			'lockPrices'     => false,
		]);

		$this->get('/mining/asteroids', $this->headers);

		$this->assertResponseStatus(200);

		$response = json_decode($this->response->getContent(), true);

		$this->assertArraySubset([
			'typeID'       => '17471',
			'typeName'     => 'Dense Veldspar',
			'groupName'    => 'Veldspar',
			'categoryName' => 'Asteroid',
		], $response[0]);

		$this->assertArraySubset([
			'typeID'       => '17470',
			'typeName'     => 'Concentrated Veldspar',
			'groupName'    => 'Veldspar',
			'categoryName' => 'Asteroid',
		], $response[1]);

		$this->assertArraySubset([
			'typeID'       => '1230',
			'typeName'     => 'Veldspar',
			'groupName'    => 'Veldspar',
			'categoryName' => 'Asteroid',
		], $response[2]);
	}
}
