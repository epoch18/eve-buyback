<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateItemsJobTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var \GuzzleHttp\Client
	 */
	private $guzzle;

	public function setUp()
	{
		parent::setUp();

		$this->config = Mockery::mock(\Illuminate\Config\Repository::class);
		$this->app->instance(\Illuminate\Config\Repository::class, $this->config);

		$this->guzzle = Mockery::mock(\GuzzleHttp\Client::class);
		$this->app->instance(\GuzzleHttp\Client::class, $this->guzzle);
	}

	public function testHandleJita()
	{
		\App\Models\Item::create([
			'typeID'         => 34,
			'typeName'       => 'Tritanium',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		]);

		\App\Models\Item::create([
			'typeID'         => 35,
			'typeName'       => 'Pyerite',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		]);

		$this->guzzle->shouldReceive('request')->once()->andReturn($this->guzzle);
		$this->guzzle->shouldReceive('getBody')->once()->andReturn(
			'{"34":{"buy":{"weightedAverage":"7.20437908608","max":"8.8","min":"1.0","stddev":"2.16197425208","median":"7.18","volume":"3221346076.0","orderCount":"25","percentile":"8.73758915556"},"sell":{"weightedAverage":"10.0416441982","max":"1000.0","min":"8.9","stddev":"117.827969092","median":"10.4","volume":"7556760644.0","orderCount":"77","percentile":"9.25892280975"}},"35":{"buy":{"weightedAverage":"6.51633107794","max":"7.43","min":"0.01","stddev":"1.98618636017","median":"6.43","volume":"899982420.0","orderCount":"19","percentile":"7.42171719421"},"sell":{"weightedAverage":"8.38380031626","max":"780.0","min":"7.6","stddev":"127.912760493","median":"9.665","volume":"15266535723.0","orderCount":"38","percentile":"7.88693704128"}}}'
		);

		$job = app()->make(\App\Jobs\UpdateItemsJob::class);
		$job->handle();

		$tritanium = \App\Models\Item::where('typeID', 34)->first();
		$pyerite = \App\Models\Item::where('typeID', 35)->first();

		$this->assertEquals(8.74, $tritanium->buyPrice);
		$this->assertEquals(9.26, $tritanium->sellPrice);

		$this->assertEquals(7.42, $pyerite->buyPrice);
		$this->assertEquals(7.89, $pyerite->sellPrice);
	}

	public function testHandle1DQ1A()
	{
		\App\Models\Item::create([
			'typeID'         => 34,
			'typeName'       => 'Tritanium',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
			'source'         => '1DQ1-A',
		]);

		\App\Models\Item::create([
			'typeID'         => 35,
			'typeName'       => 'Pyerite',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
			'source'         => '1DQ1-A',
		]);

		$this->guzzle->shouldReceive('request')->once()->andReturn($this->guzzle);
		$this->guzzle->shouldReceive('getBody')->once()->andReturn(
			'<goonmetrics method="price_data" version="1.0">
				<price_data>

					<type id="34">
						<updated>2020-04-18T20:20:50Z</updated>
						<all>
							<weekly_movement>5226777445.2</weekly_movement>
						</all>
						<buy>
							<max>9.04</max>
							<listed>2513372739</listed>
						</buy>
						<sell>
							<min>11.62</min>
							<listed>3038650349</listed>
						</sell>
					</type>

					<type id="35">
						<updated>2020-04-18T20:20:50Z</updated>
						<all>
							<weekly_movement>1630800029.6</weekly_movement>
						</all>
						<buy>
							<max>1.78</max>
							<listed>1334964575</listed>
						</buy>
						<sell>
							<min>2.32</min>
							<listed>1670507635</listed>
						</sell>
					</type>

				</price_data>
			</goonmetrics>'
		);

		$job = app()->make(\App\Jobs\UpdateItemsJob::class);
		$job->handle();

		$tritanium = \App\Models\Item::where('typeID', 34)->first();
		$pyerite = \App\Models\Item::where('typeID', 35)->first();

		$this->assertEquals(9.04, $tritanium->buyPrice);
		$this->assertEquals(11.62, $tritanium->sellPrice);

		$this->assertEquals(1.78, $pyerite->buyPrice);
		$this->assertEquals(2.32, $pyerite->sellPrice);
	}
}
