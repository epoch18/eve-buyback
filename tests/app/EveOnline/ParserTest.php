<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParserTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
	}

	public function testConvertTextToItems()
	{
		$parser = app()->make(\App\EveOnline\Parser::class);
		$text   = file_get_contents(__DIR__.'/paste01.txt');
		$items  = $parser->convertTextToItems($text);

		$this->assertEquals(151, count($items));
	}

	public function testConvertContractToItems()
	{
		$parser = app()->make(\App\EveOnline\Parser::class);

		$contract = Mockery::mock(\App\Models\API\Contract::class);
		$items    = [];

		for ($i = 34; $i <= 40; $i++) {
			$item = Mockery::mock(\App\Models\API\ContractItem::class);
			$item->shouldReceive('getAttribute')->with('quantity')->once()->andReturn(10);
			$item->shouldReceive('getAttribute')->with('type'    )->once()->andReturn(
				(object)['typeID' => $i]);
			$items[] = $item;
		}

		$contract->shouldReceive('getAttribute')->with('items')->once()->andReturn($items);

		$items = $parser->convertContractToItems($contract);

		$this->assertEquals(7, count($items));
	}
}
