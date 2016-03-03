<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RefineryTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var Illuminate\Config\Repository
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

		$tritanium = \App\Models\SDE\InvType::find(34);
		$garbage   = \App\Models\SDE\InvType::find(41);

		$this->assertEquals(true , $refinery->canBeBoughtRaw($tritanium));
		$this->assertEquals(false, $refinery->canBeBoughtRaw($garbage  ));
	}

	public function testCanBeRecycledAndBought()
	{
		$refinery = app()->make(\App\EveOnline\Refinery::class);

		$kestrel = \App\Models\SDE\InvType::find(602);
		$erebus  = \App\Models\SDE\InvType::find(671);

		$this->assertEquals(true , $refinery->canBeRecycledAndBought($kestrel));
		$this->assertEquals(false, $refinery->canBeRecycledAndBought($erebus ));
	}

	public function testCanBeRefinedAndBought()
	{
		$refinery  = app()->make(\App\EveOnline\Refinery::class);

		$veldspar = \App\Models\SDE\InvType::find(1230 );
		$glitter  = \App\Models\SDE\InvType::find(16267);
		$erebus   = \App\Models\SDE\InvType::find(671  );

		$this->assertEquals(true , $refinery->canBeRefinedAndBought($veldspar));
		$this->assertEquals(false, $refinery->canBeRefinedAndBought($glitter ));
		$this->assertEquals(false, $refinery->canBeRefinedAndBought($erebus  ));
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

		$this->assertArraySubset([
			34    => 1739811,
			35    =>  495292,
			36    =>  118711,
			37    =>   30472,
			38    =>    8571,
			39    =>    1448,
			40    =>     587,
			11399 =>       0,
		], $result->materials);

		$this->assertEquals(2394892, (integer)$result->totalValue );
		$this->assertEquals(2155402, (integer)$result->totalModded);
		$this->assertEquals( 239489, (integer)$result->totalProfit);
	}
}
