<?php

namespace App\Http\Controllers;

use App\EveOnline\Parser;
use App\EveOnline\Refinery;
use App\Models\Item;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
	/**
	* @var \App\Models\Item
	*/
	private $item;

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
	* @param  \App\Models\Item         $item
	* @param  \App\EveOnline\Parser    $parser
	* @param  \App\EveOnline\Refinery  $refinery
	* @param  \Illuminate\Http\Request $request
	*/
	public function __construct(Item $item, Parser $parser, Refinery $refinery, Request $request)
	{
		$this->item     = $item;
		$this->parser   = $parser;
		$this->refinery = $refinery;
		$this->request  = $request;
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

		return view('home.index')
			->withBuying ($buying )
			->withSelling($selling);
	}

	public function paste()
	{
		$paste   = $this->request->input('pasteData');
		$items   = $this->parser->convertTextToItems($paste);
		$buyback = $this->refinery->calculateBuyback($items);

		return view('home.index')->withBuyback($buyback);
	}
}
