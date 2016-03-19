<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemControllerTest extends TestCase {
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

		$this->get ('/manage/item/get'   , $this->headers)->assertResponseStatus(401);
		$this->post('/manage/item/add'   , $this->headers)->assertResponseStatus(302);
		$this->post('/manage/item/edit'  , $this->headers)->assertResponseStatus(302);
		$this->post('/manage/item/remove', $this->headers)->assertResponseStatus(302);
		$this->post('/manage/item/update', $this->headers)->assertResponseStatus(302);
	}

	public function testMustBeAdministrator()
	{
		$user = auth()->user();
		$user->flags = ~\App\Models\User::ADMINISTRATOR;
		$user->save();

		$this->get ('/manage/item/get'   , $this->headers)->assertResponseStatus(401);
		$this->post('/manage/item/add'   , $this->headers)->assertResponseStatus(401);
		$this->post('/manage/item/edit'  , $this->headers)->assertResponseStatus(401);
		$this->post('/manage/item/remove', $this->headers)->assertResponseStatus(401);
		$this->post('/manage/item/update', $this->headers)->assertResponseStatus(401);
	}

	public function testMustBeAjaxRequest()
	{
		$this->post('/manage/item/add')->assertResponseStatus(500);
	}

	public function testGetItems()
	{
		\App\Models\Item::create([
			'typeID'         => 34,
			'typeName'       => 'Tritanium',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		]);

		\App\Models\Item::create([
			'typeID'         => 35,
			'typeName'       => 'Pyerite',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		]);

		$this->get('/manage/item/get', $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJson();

		$data = json_decode($this->response->getContent(), true);

		$this->assertEquals(2, count($data));

		$this->assertArraySubset([
			'typeID'         => 34,
			'typeName'       => 'Tritanium',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		], $data[0]);

		$this->assertArraySubset([
			'typeID'         => 35,
			'typeName'       => 'Pyerite',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		], $data[1]);
	}

	public function testAddItemTypes()
	{
		$this->post('/manage/item/add', [
			'types'          => [34, 35, 36, 37, 38, 39, 40],
			'groups'         => [],
			'categories'     => [],
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'lockPrices'     => false,
			], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);

		foreach ([34, 35, 36, 37, 38, 39, 40] as $typeID) {
			$this->seeInDatabase('buyback_items', [
				'typeID'         => $typeID,
				'buyRaw'         => false,
				'buyRecycled'    => false,
				'buyRefined'     => false,
				'buyModifier'    => 1.0,
				'buyPrice'       => 0.0,
				'sell'           => false,
				'sellModifier'   => 1.0,
				'sellPrice'      => 0.0,
				'lockPrices'     => false,
			]);
		}
	}

	public function testAddItemGroups()
	{
		$this->post('/manage/item/add', [
			'types'          => [],
			'groups'         => [18],
			'categories'     => [],
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'lockPrices'     => false,
			], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);

		foreach ([34, 35, 36, 37, 38, 39, 40] as $typeID) {
			$this->seeInDatabase('buyback_items', [
				'typeID'         => $typeID,
				'buyRaw'         => false,
				'buyRecycled'    => false,
				'buyRefined'     => false,
				'buyModifier'    => 1.0,
				'buyPrice'       => 0.0,
				'sell'           => false,
				'sellModifier'   => 1.0,
				'sellPrice'      => 0.0,
				'lockPrices'     => false,
			]);
		}
	}

	public function testAddItemCategories()
	{
		$this->post('/manage/item/add', [
			'types'          => [],
			'groups'         => [],
			'categories'     => [4],
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'lockPrices'     => false,
			], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);

		foreach ([34, 35, 36, 37, 38, 39, 40] as $typeID) {
			$this->seeInDatabase('buyback_items', [
				'typeID'         => $typeID,
				'buyRaw'         => false,
				'buyRecycled'    => false,
				'buyRefined'     => false,
				'buyModifier'    => 1.0,
				'buyPrice'       => 0.0,
				'sell'           => false,
				'sellModifier'   => 1.0,
				'sellPrice'      => 0.0,
				'lockPrices'     => false,
			]);
		}
	}

	public function testEditItems()
	{
		\App\Models\Item::create([
			'typeID'         => 34,
			'typeName'       => 'Tritanium',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		]);

		\App\Models\Item::create([
			'typeID'         => 35,
			'typeName'       => 'Pyerite',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		]);

		$this->post('/manage/item/edit', [
				'items'          => '34',
				'buyRaw'         => true,
				'buyRecycled'    => true,
				'buyRefined'     => true,
				'buyModifier'    => 0.9,
				'buyPrice'       => 5.0,
				'sell'           => true,
				'sellModifier'   => 1.1,
				'sellPrice'      => 6.0,
				'lockPrices'     => true,
			], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);

		$this->seeInDatabase('buyback_items', [
			'typeID'         => 34,
			'buyRaw'         => true,
			'buyRecycled'    => true,
			'buyRefined'     => true,
			'buyModifier'    => 0.9,
			'buyPrice'       => 5.0,
			'sell'           => true,
			'sellModifier'   => 1.1,
			'sellPrice'      => 6.0,
			'lockPrices'     => true,
		]);

		$this->post('/manage/item/edit', [
				'items'        => '34,35',
				'buyRaw'         => true,
				'buyRecycled'    => true,
				'buyRefined'     => true,
				'buyModifier'    => 0.8,
				'buyPrice'       => 5.0,
				'sell'           => true,
				'sellModifier'   => 1.2,
				'sellPrice'      => 6.0,
				'lockPrices'     => false,
			], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);

		$this->seeInDatabase('buyback_items', [
			'typeID'         => 34,
			'buyRaw'         => true,
			'buyRecycled'    => true,
			'buyRefined'     => true,
			'buyModifier'    => 0.8,
			'buyPrice'       => 5.0,
			'sell'           => true,
			'sellModifier'   => 1.2,
			'sellPrice'      => 6.0,
			'lockPrices'     => false,
		]);

		$this->seeInDatabase('buyback_items', [
			'typeID'         => 35,
			'buyRaw'         => true,
			'buyRecycled'    => true,
			'buyRefined'     => true,
			'buyModifier'    => 0.8,
			'buyPrice'       => 0.0,
			'sell'           => true,
			'sellModifier'   => 1.2,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		]);
	}

	public function testRemoveItems()
	{
		\App\Models\Item::create([
			'typeID'         => 34,
			'typeName'       => 'Tritanium',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		]);

		\App\Models\Item::create([
			'typeID'         => 35,
			'typeName'       => 'Pyerite',
			'buyRaw'         => false,
			'buyRecycled'    => false,
			'buyRefined'     => false,
			'buyModifier'    => 1.0,
			'buyPrice'       => 0.0,
			'sell'           => false,
			'sellModifier'   => 1.0,
			'sellPrice'      => 0.0,
			'lockPrices'     => false,
		]);

		$this->post('/manage/item/remove', ['types' => '35'], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);

		$this->seeInDatabase   ('buyback_items', ['typeID' => 34]);
		$this->notSeeInDatabase('buyback_items', ['typeID' => 35]);
	}

	public function testUpdateItemPrices()
	{
		$dispatcher = Mockery::mock(\Illuminate\Bus\Dispatcher::class);
		$this->app->instance(\Illuminate\Bus\Dispatcher::class, $dispatcher);

		$job = Mockery::mock(\App\Jobs\UpdateItemsJob::class);
		$this->app->instance(\App\Jobs\UpdateItemsJob::class, $job);

		$dispatcher->shouldReceive('dispatchNow')->once()->with($job);

		$this->post('/manage/item/update', [], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);
	}
}
