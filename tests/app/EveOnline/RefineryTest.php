<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RefineryTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var \Illuminate\Config\Repository
	 */
	public $config;

	public function setUp()
	{
		parent::setUp();

		Artisan::call('db:seed', ['--class' => 'ItemSeeder']);

		$this->config = Mockery::mock(\Illuminate\Config\Repository::class);
		$this->app->instance(\Illuminate\Config\Repository::class, $this->config);

		$this->config->shouldReceive('get')->with('refinery.station_tax'  )->andReturn(1.00);
		$this->config->shouldReceive('get')->with('refinery.station_yield')->andReturn(0.60);
		$this->config->shouldReceive('get')->with('refinery.beancounter'  )->andReturn(4   );
		$this->config->shouldReceive('get')->andReturn(5);
	}

	public function testCanBeBoughtRaw()
	{
		$refinery  = app()->make(\App\EveOnline\Refinery::class);

		$buyback_items = \App\Models\Item::with('type')->get();
		$tritanium     = \App\Models\SDE\InvType::find(34);
		$garbage       = \App\Models\SDE\InvType::find(41);

		$this->assertEquals(true , $refinery->canBeBoughtRaw($tritanium, $buyback_items));
		$this->assertEquals(false, $refinery->canBeBoughtRaw($garbage  , $buyback_items));
	}

	public function testCanBeRecycledAndBought()
	{
		$refinery = app()->make(\App\EveOnline\Refinery::class);

		$buyback_items = \App\Models\Item::with('type')->get();
		$kestrel       = \App\Models\SDE\InvType::find(602);
		$erebus        = \App\Models\SDE\InvType::find(671);

		$this->assertEquals(true , $refinery->canBeRecycledAndBought($kestrel, $buyback_items));
		$this->assertEquals(false, $refinery->canBeRecycledAndBought($erebus , $buyback_items));
	}

	public function testCanBeRefinedAndBought()
	{
		$refinery  = app()->make(\App\EveOnline\Refinery::class);

		$buyback_items = \App\Models\Item::with('type')->get();
		$veldspar      = \App\Models\SDE\InvType::find(1230 );
		$glitter       = \App\Models\SDE\InvType::find(16267);
		$erebus        = \App\Models\SDE\InvType::find(671  );

		$this->assertEquals(true , $refinery->canBeRefinedAndBought($veldspar, $buyback_items));
		$this->assertEquals(false, $refinery->canBeRefinedAndBought($glitter , $buyback_items));
		$this->assertEquals(false, $refinery->canBeRefinedAndBought($erebus  , $buyback_items));
	}

	public function testGetRecyledMaterials()
	{
		$refinery = app()->make(\App\EveOnline\Refinery::class);

		$kestrel   = \App\Models\SDE\InvType::find(602);
		$materials = $refinery->getRecycledMaterials($kestrel);

		foreach ($materials as &$material) {
			$material = (integer)$material;
		}

		$this->assertArraySubset([
			34 => 10169,
			35 =>  3178,
			36 =>  1652,
			37 =>   635,
			38 =>    32,
			39 =>    12,
		], $materials);

		$emp       = \App\Models\SDE\InvType::find(201);
		$materials = $refinery->getRecycledMaterials($emp);

		foreach ($materials as &$material) {
			$material = (integer)$material;
		}

		$this->assertArraySubset([
			34 => 1017,
			35 =>   72,
			36 =>   50,
			37 =>   16,
		], $materials);
	}

	public function testGetRefinedMaterials()
	{
		$refinery = app()->make(\App\EveOnline\Refinery::class);

		$spodumain = \App\Models\SDE\InvType::find(19);
		$materials = $refinery->getRefinedMaterials($spodumain);

		foreach ($materials as &$material) {
			$material = (integer)$material;
		}

		$this->assertArraySubset([
			34 => 48624,
			35 => 10462,
			36 =>  1823,
			37 =>   390,
		], $materials);
	}

	public function testCalculateBuyback()
	{
		$refinery = app()->make(\App\EveOnline\Refinery::class);
		$parser   = app()->make(\App\EveOnline\Parser::class);
		$text     = file_get_contents(__DIR__.'/paste01.txt');
		$items    = $parser->convertTextToItems($text);
		$result   = $refinery->calculateBuyback($items);

		// $this->assertEquals(1739811, $result->materials[   34]->quantity);
		// $this->assertEquals( 495292, $result->materials[   35]->quantity);
		// $this->assertEquals( 118711, $result->materials[   36]->quantity);
		// $this->assertEquals(  30472, $result->materials[   37]->quantity);
		// $this->assertEquals(   8571, $result->materials[   38]->quantity);
		// $this->assertEquals(   1448, $result->materials[   39]->quantity);
		// $this->assertEquals(    587, $result->materials[   40]->quantity);
		// $this->assertEquals(      0, $result->materials[11399]->quantity);

		$this->assertEquals(1730939, $result->materials[   34]->quantity);
		$this->assertEquals( 491945, $result->materials[   35]->quantity);
		$this->assertEquals( 117894, $result->materials[   36]->quantity);
		$this->assertEquals(  30183, $result->materials[   37]->quantity);
		$this->assertEquals(   8479, $result->materials[   38]->quantity);
		$this->assertEquals(   1447, $result->materials[   39]->quantity);
		$this->assertEquals(    585, $result->materials[   40]->quantity);
		$this->assertEquals(      0, $result->materials[11399]->quantity);

		$this->assertEquals(2381472, (integer)$result->totalValue      );
		$this->assertEquals(2143324, (integer)$result->totalValueModded);
	}
}
