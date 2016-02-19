<?php

namespace App\Console\Commands;

use App\Jobs\UpdateAPI as Job;
use Illuminate\Console\Command;
use Illuminate\Bus\Dispatcher;

class UpdateAPI extends Command
{
	/**
	 * @var \Illuminate\Bus\Dispatcher
	 */
	private $dispatcher;

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
	 * @return void
	 */
	public function __construct(Dispatcher $dispatcher)
	{
		parent::__construct();

		$this->dispatcher = $dispatcher;
	}

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle()
	{
		$this->dispatcher->dispatchNow(new Job);
	}
}
