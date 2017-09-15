<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Item;
use App\Models\SDE\InvType;
use DB;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Log;
use Pheal\Pheal;

class UpdateItemsJob extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	/**
	* @var \Illuminate\Config\Repository
	*/
	private $config;

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
	 * @param  \Illuminate\Config\Repository $config
	 * @param  \GuzzleHttp\Client            $guzzle
	 * @param  \App\Models\Item              $item
	 * @param  \App\Models\SDE\InvType       $type
	 * @return void
	 */
	public function __construct(Config $config, Guzzle $guzzle, Item $item, InvType $type)
	{
		$this->config = $config;
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

			foreach ($this->item->all()->chunk(500) as $chunk) {
				$url = config('services.fuzzworks.url');

				if (config('services.fuzzworks.usestation')) {
                    $url .= 'station=' . config('services.fuzzworks.usestation');
                } else {
                    $url .= 'region=' . config('services.fuzzworks.useregion');
                }

                $url .= "&types=";
				foreach ($chunk as $item) {
					$url .= "{$item->typeID},";
				}

				$url = substr($url, 0, -1);

				Log::info("Fetching prices using url: {$url}");

				$response = $this->guzzle->request('GET', $url);
				$response = json_decode($response->getBody());

				Log::info('Updating records.');

				DB::transaction(function () use ($response) {
					$buy  = config('services.fuzzworks.buy' );
					$sell = config('services.fuzzworks.sell');

					foreach ($response as $typeID => $values) {
						$item = $this->item->find((int)$typeID);

						if (!$item->lockPrices) {
							$item->update([
								//'typeName'  => $item->type->typeName,
								'buyPrice'  => (double)$values->buy->$buy,
								'sellPrice' => (double)$values->sell->$sell,
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
