<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HelperTest extends TestCase
{
	use DatabaseMigrations;

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

	public function setUp()
	{
		parent::setUp();

		$this->cache = Mockery::mock(\Illuminate\Cache\Repository::class);
		$this->app->instance(\Illuminate\Cache\Repository::class, $this->cache);

		$this->carbon = Mockery::mock(\Carbon\Carbon::class);
		$this->app->instance(\Carbon\Carbon::class, $this->carbon);

		$this->pheal = Mockery::mock(\Pheal\Pheal::class);
		$this->app->instance(\Pheal\Pheal::class, $this->pheal);

		$this->outpost = Mockery::mock(\App\Models\API\Outpost::class);
		$this->app->instance(\App\Models\API\Outpost::class, $this->outpost);

		$this->station = Mockery::mock(\App\Models\SDE\StaStation::class);
		$this->app->instance(\App\Models\SDE\StaStation::class, $this->station);
	}

	public function testConvertStationIdToModel()
	{
		$helper = app()->make(\App\EveOnline\Helper::class);

		$model = (object)[
			'stationID'   => 12345,
			'stationName' => 'Station Name',
		];

		$this->carbon->shouldReceive('now')->andReturn($this->carbon);
		$this->carbon->shouldReceive('addHours')->andReturn(12345);

		$this->cache->shouldReceive('has')->with("station:12345678")->once()->andReturn(false);
		$this->cache->shouldReceive('put')->with("station:12345678", $model, 12345)->once();

		$this->station->shouldReceive('where')->once()->with('stationID', 12345678)
			->andReturn($this->station);
		$this->station->shouldReceive('first')->once()
			->andReturn(false);

		$this->outpost->shouldReceive('where')->once()->with('stationID', 12345678)
			->andReturn($this->outpost);
		$this->outpost->shouldReceive('first')->once()
			->andReturn($model);

		$station = $helper->convertStationIdToModel(12345678);
		$this->assertEquals('Station Name', $station->stationName);

		$this->cache->shouldReceive('has')->with("station:12345678")->once()->andReturn(true);
		$this->cache->shouldReceive('get')->with("station:12345678")->once()->andReturn($model);

		$name = $helper->convertStationIdToModel(12345678);
		$this->assertEquals('Station Name', $model->stationName);
	}

	public function testConvertCharacterIdToName()
	{
		$helper = app()->make(\App\EveOnline\Helper::class);

		$this->carbon->shouldReceive('now')->andReturn($this->carbon);
		$this->carbon->shouldReceive('addHours')->andReturn(12345);

		$this->cache->shouldReceive('has')->with("character:12345678")->once()->andReturn(false);
		$this->cache->shouldReceive('put')->with("character:12345678", "Character Name", 12345)->once();

		$this->pheal->shouldReceive('CharacterName')->once()->andReturn((object)[
			'characters' => [
				(object)['name' => 'Character Name'],
			],
		]);

		$name = $helper->convertCharacterIdToName(12345678);
		$this->assertEquals('Character Name', $name);

		$this->cache->shouldReceive('has')->with("character:12345678")->once()->andReturn(true);
		$this->cache->shouldReceive('get')->with("character:12345678")->once()->andReturn('Character Name');

		$name = $helper->convertCharacterIdToName(12345678);
		$this->assertEquals('Character Name', $name);
	}
}
