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
}
