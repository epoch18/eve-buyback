<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ResourceControllerTest extends TestCase {
	use DatabaseMigrations;

	private $headers;

	public function setUp()
	{
		parent::setUp();

		$this->headers = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];

		$user = factory(App\Models\User::class)->make();
		$user->setAdministrator(true)->save();
		auth()->login($user);
	}

	public function testMustBeLoggedIn()
	{
		auth()->logout();

		$this->get('/manage/resource/categories', $this->headers)->assertResponseStatus(401);
		$this->get('/manage/resource/groups'    , $this->headers)->assertResponseStatus(401);
		$this->get('/manage/resource/types'     , $this->headers)->assertResponseStatus(401);
	}

	public function testMustBeAdministrator()
	{
		$user = auth()->user();
		$user->flags = ~\App\Models\User::ADMINISTRATOR;
		$user->save();

		$this->get('/manage/resource/categories', $this->headers)->assertResponseStatus(401);
		$this->get('/manage/resource/groups'    , $this->headers)->assertResponseStatus(401);
		$this->get('/manage/resource/types'     , $this->headers)->assertResponseStatus(401);
	}

	public function testGetCategories()
	{
		$this->get('/manage/resource/categories?query=Material&page=1', $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure([
			'total',
			'per_page',
			'current_page',
			'last_page',
			'next_page_url',
			'prev_page_url',
			'from',
			'to',
			'data',
		]);

		$this->seeJson([
			'total'         => 1,
			'per_page'      => 20,
			'current_page'  => 1,
			'last_page'     => 1,
			'next_page_url' => null,
			'prev_page_url' => null,
			'from'          => 1,
			'to'            => 1,
		]);

		$response = json_decode($this->response->getContent(), true);

		$this->assertEquals(1, count($response['data']));

		$this->assertArraySubset([[
				'categoryID'   => 4,
				'categoryName' => 'Material',
			]],
			$response['data']
		);
	}

	public function testGetGroups()
	{
		$this->get('/manage/resource/groups?query=Mineral&page=1', $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure([
			'total',
			'per_page',
			'current_page',
			'last_page',
			'next_page_url',
			'prev_page_url',
			'from',
			'to',
			'data',
		]);

		$this->seeJson([
			'total'         => 1,
			'per_page'      => 20,
			'current_page'  => 1,
			'last_page'     => 1,
			'next_page_url' => null,
			'prev_page_url' => null,
			'from'          => 1,
			'to'            => 1,
		]);

		$response = json_decode($this->response->getContent(), true);

		$this->assertEquals(1, count($response['data']));

		$this->assertArraySubset([[
				'groupID'   => 18,
				'groupName' => 'Mineral',
			]],
			$response['data']
		);
	}

	public function testGetTypes()
	{
		$this->get('/manage/resource/types?query=Tritanium&page=1', $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure([
			'total',
			'per_page',
			'current_page',
			'last_page',
			'next_page_url',
			'prev_page_url',
			'from',
			'to',
			'data',
		]);

		$this->seeJson([
			'total'         => 4,
			'per_page'      => 20,
			'current_page'  => 1,
			'last_page'     => 1,
			'next_page_url' => null,
			'prev_page_url' => null,
			'from'          => 1,
			'to'            => 4,
		]);

		$response = json_decode($this->response->getContent(), true);

		$this->assertEquals(4, count($response['data']));

		$this->assertArraySubset([[
				'typeID'   => 34,
				'typeName' => 'Tritanium',
			]],
			$response['data']
		);
	}
}
