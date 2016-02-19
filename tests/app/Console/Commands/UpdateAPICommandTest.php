<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateAPICommandTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var \Illuminate\Bus\Dispatcher
	 */
	private $dispatcher;

	/**
	 * @var \App\Jobs\UpdateAPIJob
	 */
	private $job;

	public function setUp()
	{
		parent::setUp();

		$this->dispatcher = Mockery::mock(\Illuminate\Bus\Dispatcher::class);
		$this->job        = Mockery::mock(\App\Jobs\UpdateAPIJob::class);
	}

	public function testHandle()
	{
		$this->dispatcher->shouldReceive('dispatchNow')->once()->with($this->job);

		$command = new \App\Console\Commands\UpdateAPICommand($this->dispatcher, $this->job);
		$command->handle();
	}
}
