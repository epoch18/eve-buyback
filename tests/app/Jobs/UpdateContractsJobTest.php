<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateContractsJobTest extends TestCase
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
		$this->pheal->shouldReceive('scope')->atleast(1);

		// Mock account objects.
		$key = Mockery::mock(stdClass::class);
		$key->accessMask = 12345;
		$key->type       = 'Corporation';

		$apiKeyInfo = Mockery::mock(stdClass::class);
		$apiKeyInfo->key = (object)[
			'accessMask' => 12345,
			'type'       => 'Corporation',
		];

		$this->pheal->shouldReceive('ApiKeyInfo')->once()->andReturn($apiKeyInfo);

		// Mock contract objects.
		$contracts = Mockery::mock(stdClass::class);
		$contracts->contractList = [
			(object)[
				'title'          => 'MWD Scimitar + Kin Hardener - Rigs in cargo',
				'volume'         => '89000',
				'buyout'         => '0.00',
				'collateral'     => '0.00',
				'reward'         => '0.00',
				'price'          => '220000000.00',
				'dateCompleted'  => '2015-10-16 04:36:30',
				'numDays'        => '0',
				'dateAccepted'   => '2015-10-16 04:36:30',
				'dateExpired'    => '2015-10-23 15:32:31',
				'dateIssued'     => '2015-10-09 15:32:31',
				'availability'   => 'Private',
				'forCorp'        => '0',
				'status'         => 'Completed',
				'type'           => 'ItemExchange',
				'endStationID'   => '60015108',
				'startStationID' => '60015108',
				'acceptorID'     => '258695360',
				'assigneeID'     => '386292982',
				'issuerCorpID'   => '673319797',
				'issuerID'       => '91512526',
				'contractID'     => '97809127',
			],
		];

		$this->pheal->shouldReceive('Contracts')->once()->andReturn($contracts);

		$contract_items = Mockery::mock(stdClass::class);
		$contract_items->itemList = [
			(object)[
				'included'    => '1',
				'singleton'   => '0',
				'quantity'    => '1',
				'rawQuantity' => '1',
				'typeID'      => '4310',
				'recordID'    => '1737516979',
			],
			(object)[
				'included'    => '1',
				'singleton'   => '0',
				'quantity'    => '1',
				'rawQuantity' => '1',
				'typeID'      => '35659',
				'recordID'    => '1737516980',
			],
			(object)[
				'included'    => '1',
				'singleton'   => '0',
				'quantity'    => '1',
				'rawQuantity' => '1',
				'typeID'      => '519',
				'recordID'    => '1737516981',
			],
		];

		$this->pheal->shouldReceive('ContractItems')->once()->andReturn($contract_items);

		$job = app()->make(App\Jobs\UpdateContractsJob::class);
		$job->handle();

		$this->seeInDatabase('api_contracts'     , ['contractID' => 97809127  ]);
		$this->seeInDatabase('api_contract_items', ['recordID'   => 1737516979]);
		$this->seeInDatabase('api_contract_items', ['recordID'   => 1737516980]);
		$this->seeInDatabase('api_contract_items', ['recordID'   => 1737516981]);
	}
}
