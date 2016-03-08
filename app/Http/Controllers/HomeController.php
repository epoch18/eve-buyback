<?php

namespace App\Http\Controllers;

use App\EveOnline\Parser;
use App\EveOnline\Refinery;
use App\Models\Item;
use App\Models\Setting;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Michelf\Markdown;

class HomeController extends Controller
{
	/**
	* @var \App\Models\Item
	*/
	private $item;

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
	* @var \App\Models\Setting
	*/
	private $setting;


	/**
	* Constructs the class.
	* @param  \App\Models\Item         $item
	* @param  \Michelf\Markdown        $markdown
	* @param  \App\EveOnline\Parser    $parser
	* @param  \App\EveOnline\Refinery  $refinery
	* @param  \Illuminate\Http\Request $request
	* @param  \App\Models\Setting      $setting
	*/
	public function __construct(
		Item     $item,
		Markdown $markdown,
		Parser   $parser,
		Refinery $refinery,
		Request  $request,
		Setting  $setting
	) {
		$this->item     = $item;
		$this->markdown = $markdown;
		$this->parser   = $parser;
		$this->refinery = $refinery;
		$this->request  = $request;
		$this->setting  = $setting;
	}

	public function index()
	{
		$buying = $this->item
			->with('type')
			->with('type.group')
			->with('type.group.category')
			->buying()
			->get();

		$selling = $this->item
			->with('type')
			->with('type.group')
			->with('type.group.category')
			->selling()
			->get();

		$motd = $this->setting->where('key', 'motd')->first();
		$motd = $motd ? $this->markdown->defaultTransform($motd->value) : false;

		return view('home.index')
			->withBuying ($buying )
			->withSelling($selling)
			->withMotd   ($motd   )
		;
	}

	public function paste()
	{
		$paste   = $this->request->input('pasteData');
		$items   = $this->parser->convertTextToItems($paste);
		$buyback = $this->refinery->calculateBuyback($items);

		return view('home.paste')->withBuyback($buyback);
	}
}
