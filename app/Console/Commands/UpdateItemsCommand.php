<?php

namespace App\Console\Commands;

use App\Jobs\UpdateItemsJob;
use Illuminate\Console\Command;
use Illuminate\Bus\Dispatcher;

class UpdateItemsCommand extends Command
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
	protected $signature = 'buyback:update-items';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Retrieves and stores market data for buyback items.';

	/**
	 * Create a new command instance.
	 * @param  \Illuminate\Bus\Dispatcher $dispatcher
	 * @param  \App\Jobs\UpdateItemsJob   $job
	 * @return void
	 */
	public function __construct(Dispatcher $dispatcher, UpdateItemsJob $job)
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
