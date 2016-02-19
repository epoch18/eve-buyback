<?php

namespace App\Console\Commands;

use App\Jobs\UpdateAPIJob;
use Illuminate\Console\Command;
use Illuminate\Bus\Dispatcher;

class UpdateAPICommand extends Command
{
	/**
	 * @var \Illuminate\Bus\Dispatcher
	 */
	private $dispatcher;

	/**
	 * @var \App\Jobs\UpdateAPIJob
	 */
	private $job;

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'buyback:update-api';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Retrieves and stores all relevant api information.';

	/**
	 * Create a new command instance.
	 * @param  \Illuminate\Bus\Dispatcher $dispatcher
	 * @param  \App\Jobs\UpdateAPIJob     $job
	 * @return void
	 */
	public function __construct(Dispatcher $dispatcher, UpdateAPIJob $job)
	{
		parent::__construct();

		$this->dispatcher = $dispatcher;
		$this->job        = $job;
	}

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle()
	{
		$this->dispatcher->dispatchNow($this->job);
	}
}
