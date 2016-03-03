<?php

namespace App\Http\Controllers;

use App\EveOnline\Parser;
use App\EveOnline\Refinery;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SDE\InvType;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
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
	* @var \App\Models\SDE\InvType
	*/
	private $type;

	/**
	* Constructs the class.
	* @param  \App\EveOnline\Parser    $parser
	* @param  \App\EveOnline\Refinery  $refinery
	* @param  \Illuminate\Http\Request $request
	* @param  \App\Models\Setting      $setting
	* @param  \App\Models\SDE\InvType  $type
	*/
	public function __construct(Parser $parser, Refinery $refinery, Request $request, Setting $setting, InvType $type)
	{
		$this->parser   = $parser;
		$this->refinery = $refinery;
		$this->request  = $request;
		$this->setting  = $setting;
		$this->type     = $type;
	}

	public function index()
	{
		return view('home.index');
	}

	public function paste()
	{
		$paste  = $this->request->input('pasteData');
		$items  = $this->parser->convertTextToItems($paste);
		$result = $this->refinery->calculateBuyback($items);

		dd($result);


		/*$types->each(function ($type) {
			dd($type);
		});

		$refinables = $types->filter(function ($value, $key) {
			return $value['type']->group->category->categoryName == 'Asteroid';
		});

		$recyclables = $types->filter(function ($value, $key) {
			$category = $value['type']->group->category->categoryName;

			return $category == 'Module' || $category == 'Ship' || $category == 'Charge';
		});



		return $recyclables;*/
	}

	/**
	 * Parses pasted data and returns and array of type models and quantities.
	 * @param  string $data
	 * @return \Illuminate\Support\Collection
	 */
	private function parsePastedData($data)
	{
		$types = [];

		foreach ($data as $item) {
			if (preg_match_all('/(.*?)\t(\d+).*/', $item, $matches)) {
				$type = [
					'type'     => $this->type->where('typeName', $matches[1][0])->first(),
					'quantity' => $matches[2][0],
				];

				if ($type['type']) {
					$types[] = $type;
				}
		} }

		return collect($types);
	}
}
