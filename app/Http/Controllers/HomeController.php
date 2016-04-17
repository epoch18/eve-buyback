<?php

namespace App\Http\Controllers;

use App\EveOnline\Parser;
use App\EveOnline\Refinery;
use App\Models\SDE\InvCategory;
use App\Models\Item;
use App\Models\Setting;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Http\Request;
use Michelf\Markdown;

class HomeController extends Controller
{
	/**
	 * @var \App\Models\InvCategory
	 */
	private $category_model;

	/**
	 * @var \App\Models\Item
	 */
	private $item_model;

	/**
	 * @var \App\Models\Setting
	 */
	private $setting_model;

	/**
	 * @var \Illuminate\Cache\Repository
	 */
	private $cache;

	/**
	 * @var \Carbon\Carbon
	 */
	private $carbon;

	/**
	 * @var \Michelf\Markdown
	 */
	private $markdown;

	/**
	 * @var \App\EveOnline\Parser
	 */
	private $parser;

	/**
	 * @var \App\EveOnline\Refinery
	 */
	private $refinery;

	/**
	 * @var \Illuminate\Http\Request
	 */
	private $request;

	/**
	 * Constructs the class.
	 * @param  \App\Models\SDE\InvCategory $category_model
	 * @param  \App\Models\Item            $item_model
	 * @param  \App\Models\Setting         $setting
	 * @param  \Illuminate\Cache\Repository  $cache
	 * @param  \Carbon\Carbon                $carbon
	 * @param  \Michelf\Markdown           $markdown
	 * @param  \App\EveOnline\Parser       $parser
	 * @param  \App\EveOnline\Refinery     $refinery
	 * @param  \Illuminate\Http\Request    $request
	 */
	public function __construct(
		InvCategory $category_model,
		Item        $item_model,
		Setting     $setting_model,
		Cache       $cache,
		Carbon      $carbon,
		Markdown    $markdown,
		Parser      $parser,
		Refinery    $refinery,
		Request     $request
	) {
		$this->category_model = $category_model;
		$this->item_model     = $item_model;
		$this->setting_model  = $setting_model;
		$this->cache          = $cache;
		$this->carbon         = $carbon;
		$this->markdown       = $markdown;
		$this->parser         = $parser;
		$this->refinery       = $refinery;
		$this->request        = $request;
	}

	/**
	 * Handles displaying the buyback home page.
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$buying = $this->item_model
			->with('type')
			->with('type.group')
			->with('type.group.category')
			->buying()
			->get();

		$selling = $this->item_model
			->with('type')
			->with('type.group')
			->with('type.group.category')
			->selling()
			->get();

		$motd = $this->setting_model->where('key', 'motd')->first();
		$motd = $motd ? $this->markdown->defaultTransform($motd->value) : false;

		return view('home.index')
			->withBuying ($buying )
			->withSelling($selling)
			->withMotd   ($motd   )
		;
	}

	/**
	 * Handles displaying the page that shows the contract price of pasted data.
	 * @return \Illuminate\Http\Response
	 */
	public function paste()
	{
		$paste   = $this->request->input('pasteData');
		$items   = $this->parser->convertTextToItems($paste);
		$buyback = $this->refinery->calculateBuyback($items);

		return view('home.paste')->withBuyback($buyback);
	}

	/**
	 * Handles displaying the page that shows the value of asteroids being bought.
	 * @return \Illuminate\Http\Response
	 */
	public function getMiningTable()
	{
		$asteroids = json_decode($this->getAsteroids()->getContent(), true);

		return view('home.mining')->withAsteroids($asteroids);
	}

	/**
	 * Gets a list of all asteroids and their buyback values.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getAsteroids()
	{
		// Return a cached result if one exists.
		if ($this->cache->has('mining')) {
			$asteroids = $this->cache->get('mining');

			return response()->json($asteroids);
		}

		// Get all published asteroid types.
		$types = $this->category_model->where('categoryName', 'Asteroid')->first()->types;
		$types = $types->whereLoose('published', true);

		$asteroids = [];

		foreach ($types as $type) {
			// Do not show compressed asteroids.
			if (is_int(strpos($type->typeName, 'Compressed'))) {
				continue;
			}

			// Calculate the price per refined portion.
			$item     = $this->parser->convertTextToItems("{$type->typeName}\t{$type->portionSize}\n");
			$buyback  = $this->refinery->calculateBuyback($item);

			// Do not show asteroids that aren't wanted.
			if ($buyback->totalValueModded <= 0) {
				continue;
			}

			// Add the asteroid to the response array.
			$asteroids[] = [
				'typeID'       => $type->typeID,
				'typeName'     => $type->typeName,
				'groupName'    => $type->group->groupName,
				'categoryName' => $type->group->category->categoryName,
				'price'        => $buyback->totalValueModded,
			];
		}

		// Sort asteroids by descending price.
		usort($asteroids, function ($a, $b) {
			if ($a->price == $b->price) {
				return 0;
			}

			return ($a->price > $b->price) ? -1 : 1;
		});

		// Cache the result.
		$this->cache->put('mining', $asteroids, $this->carbon->now()->addMinutes(30));

		return response()->json($asteroids);
	}
}
