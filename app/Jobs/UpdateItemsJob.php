<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Item;
use App\Models\SDE\InvType;
use DB;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Pheal\Pheal;

class UpdateItemsJob extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	/**
	 * @var \GuzzleHttp\Client
	 */
	private $guzzle;

	/**
	 * @var \App\Models\Item
	 */
	private $item;

	/**
	 * @var \App\Models\SDE\InvType
	 */
	private $type;

	/**
	 * Create a new job instance.
	 * @param  \GuzzleHttp\Client      $guzzle
	 * @param  \App\Models\Item        $item
	 * @param  \App\Models\SDE\InvType $type
	 * @return void
	 */
	public function __construct(Guzzle $guzzle, Item $item, InvType $type)
	{
		$this->guzzle = $guzzle;
		$this->item   = $item;
		$this->type   = $type;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		try {
			Log::info('UpdateItemsJob started.');

			foreach ($this->item->all()->chunk(100) as $chunk) {
				$url = config('services.evecentral.url')
					. 'usesystem=' . config('services.evecentral.usesystem')
					. '&minq='     . config('services.evecentral.minq')
				;

				foreach ($chunk as $item) {
					$url .= "&typeid={$item->typeID}";
				}

				Log::info("Fetching prices using url: {$url}");

				$response = $this->guzzle->request('GET', $url);
				$response = simplexml_load_string($response->getBody());

				Log::info('Updating records.');

				DB::transaction(function () use ($response) {
					$buy  = config('services.evecentral.buy' );
					$sell = config('services.evecentral.sell');

					foreach ($response->marketstat->type as $type) {
						$item = $this->item->find((integer)$type['id']);

						if (!$item->lockPrices) {
							$item->update([
								'typeName'  => $item->type->typeName,
								'buyPrice'  => (double)$type->buy->$buy,
								'sellPrice' => (double)$type->sell->$sell,
							]); $item->touch();
						} // pricesLocked
					} // foreach
				}); // transaction
			} // chunk

			Log::info('UpdateItemsJob finished.');

		} catch (Exception $e) {
			Log::error('UpdateItemsJob failed. Throwing exception:');
			throw $e;
		}
	}
}
