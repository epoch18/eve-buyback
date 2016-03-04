<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateOutpostsJobTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var \Pheal\Pheal
	 */
	private $pheal;

	public function setUp()
	{
		parent::setUp();

		$this->pheal = Mockery::mock(\Pheal\Pheal::class);
		$this->app->instance(\Pheal\Pheal::class, $this->pheal);
	}

	public function testHandle()
	{
		$outposts = (object)[
			'outposts' => [
				(object)[
					'stationID'       => 61000722,
					'stationName'     => 'BUZ-DB',
					'stationTypeID'   => 21646,
					'solarSystemID'   => 30001245,
					'corporationID'   => 0,
					'corporationName' => '',
					'x'               => 0,
					'y'               => 0,
					'z'               => 0,
				],
				(object)[
					'stationID'       => 61000444,
					'stationName'     => 'F-88PJ',
					'stationTypeID'   => 21646,
					'solarSystemID'   => 30004653,
					'corporationID'   => 0,
					'corporationName' => '',
					'x'               => 0,
					'y'               => 0,
					'z'               => 0,
				],
				(object)[
					'stationID'       => 61000081,
					'stationName'     => 'R-YWID',
					'stationTypeID'   => 21646,
					'solarSystemID'   => 30003631,
					'corporationID'   => 0,
					'corporationName' => '',
					'x'               => 0,
					'y'               => 0,
					'z'               => 0,
				],
			],
		];

		$this->pheal->shouldReceive('scope')->atleast(1);
		$this->pheal->shouldReceive('ConquerableStationList')->once()->andReturn($outposts);

		$job = app()->make(App\Jobs\UpdateOutpostsJob::class);
		$job->handle();

		$this->seeInDatabase('api_outposts', [
			'stationID'       => 61000722,
			'stationName'     => 'BUZ-DB',
			'stationTypeID'   => 21646,
			'solarSystemID'   => 30001245,
			'corporationID'   => 0,
			'corporationName' => '',
			'x'               => 0,
			'y'               => 0,
			'z'               => 0,
		]);

		$this->seeInDatabase('api_outposts', [
			'stationID'       => 61000444,
			'stationName'     => 'F-88PJ',
			'stationTypeID'   => 21646,
			'solarSystemID'   => 30004653,
			'corporationID'   => 0,
			'corporationName' => '',
			'x'               => 0,
			'y'               => 0,
			'z'               => 0,
		]);

		$this->seeInDatabase('api_outposts', [
			'stationID'       => 61000081,
			'stationName'     => 'R-YWID',
			'stationTypeID'   => 21646,
			'solarSystemID'   => 30003631,
			'corporationID'   => 0,
			'corporationName' => '',
			'x'               => 0,
			'y'               => 0,
			'z'               => 0,
		]);
	}
}
