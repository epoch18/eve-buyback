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

		$this->guzzle = Mockery::mock(\GuzzleHttp\Client::class);
		$this->app->instance(\GuzzleHttp\Client::class, $this->guzzle);
	}

	public function testHandle()
	{
		\App\Models\Item::create([
			'typeID'         => 34,
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
			'<?xml version=\'1.0\' encoding=\'utf-8\'?>
			<evec_api version="2.0" method="marketstat_xml">
			    <marketstat>
			        <type id="34">
			            <buy>
			                <volume>16082347965</volume>
			                <avg>5.79</avg>
			                <max>6.09</max>
			                <min>2.00</min>
			                <stddev>0.84</stddev>
			                <median>5.97</median>
			                <percentile>6.08</percentile>
			            </buy>
			            <sell>
			                <volume>30902591338</volume>
			                <avg>6.47</avg>
			                <max>11.16</max>
			                <min>6.18</min>
			                <stddev>0.55</stddev>
			                <median>6.37</median>
			                <percentile>6.22</percentile>
			            </sell>
			            <all>
			                <volume>47184939303</volume>
			                <avg>6.21</avg>
			                <max>11.16</max>
			                <min>1.19</min>
			                <stddev>0.90</stddev>
			                <median>6.27</median>
			                <percentile>4.35</percentile>
			            </all>
			        </type>
			        <type id="35">
			            <buy>
			                <volume>1889008841</volume>
			                <avg>10.37</avg>
			                <max>10.71</max>
			                <min>8.21</min>
			                <stddev>0.73</stddev>
			                <median>10.48</median>
			                <percentile>10.57</percentile>
			            </buy>
			            <sell>
			                <volume>7459224523</volume>
			                <avg>12.98</avg>
			                <max>17.40</max>
			                <min>10.93</min>
			                <stddev>1.54</stddev>
			                <median>11.53</median>
			                <percentile>10.95</percentile>
			            </sell>
			            <all>
			                <volume>9348233364</volume>
			                <avg>12.45</avg>
			                <max>17.40</max>
			                <min>8.21</min>
			                <stddev>1.60</stddev>
			                <median>11.25</median>
			                <percentile>10.12</percentile>
			            </all>
			        </type>
			    </marketstat>
			</evec_api>'
		);

		$job = app()->make(App\Jobs\UpdateItemsJob::class);
		$job->handle();

		$this->seeInDatabase('buyback_items', ['typeID' => 34, 'buyPrice' =>  5.79, 'sellPrice' =>  6.47]);
		$this->seeInDatabase('buyback_items', ['typeID' => 35, 'buyPrice' => 10.37, 'sellPrice' => 12.98]);
	}
}
