<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateAPITest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var \Illuminate\Bus\Dispatcher
	 */
	private $dispatcher;

	/**
	 * @var \App\Jobs\UpdateAPI
	 */
	private $job;

	public function setUp()
	{
		parent::setUp();

		$this->dispatcher = Mockery::mock(\Illuminate\Bus\Dispatcher::class);
		$this->job        = Mockery::mock(\App\Jobs\UpdateAPI::class);
	}

	public function testHandle()
	{
		$this->dispatcher->shouldReceive('dispatchNow')->once()->with($this->job);

		$command = new \App\Console\Commands\UpdateAPI($this->dispatcher, $this->job);
		$command->handle();
	}
}
