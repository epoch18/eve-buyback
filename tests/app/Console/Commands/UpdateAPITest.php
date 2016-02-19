<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateAPITest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var App\Models\User
	 */
	private $dispatcher;

	public function setUp()
	{
		parent::setUp();

		$this->dispatcher = Mockery::mock(\Illuminate\Bus\Dispatcher::class);
	}

	public function testHandle()
	{
		$this->dispatcher->shouldReceive('dispatchNow')->once()->with(\App\Jobs\UpdateAPI::class);

		$command = new \App\Console\Commands\UpdateAPI($this->dispatcher);
		$command->handle();
	}
}
