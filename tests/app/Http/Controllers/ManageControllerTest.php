<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageControllerTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var App\Models\User
	 */
	private $user;

	public function setUp()
	{
		parent::setUp();

		$this->user = factory(App\Models\User::class)->make();
		$this->user->setAdministrator(true);
		$this->user->save();
	}

	public function testConfigureMotd()
	{
		auth()->login($this->user);

		$this->post('/config/motd', ['text' => 'test message'],
			['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->seeJson(['result' => true])
			->seeInDatabase('buyback_settings',
				['key' => 'motd', 'value' => 'test message']);

		$this->post('/config/motd', ['text' => ''],
			['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->seeJson(['result' => true])
			->notSeeInDatabase('buyback_settings',
				['key' => 'motd']);

		auth()->logout();

		$this->post('/config/motd', ['text' => 'test message'],
			['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->assertResponseStatus(401);
	}

	public function testConfigureBuybackAddItemTypes()
	{
		auth()->login($this->user);

		$this->post('/config/add-items', [
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
			], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->seeJson(['result' => true]);

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

	public function testConfigureBuybackAddItemGroups()
	{
		auth()->login($this->user);

		$this->post('/config/add-items', [
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
			], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->seeJson(['result' => true]);

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

	public function testConfigureBuybackAddItemCategories()
	{
		auth()->login($this->user);

		$this->post('/config/add-items', [
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
			], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->seeJson(['result' => true]);

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

	public function testConfigureBuybackNotLoggedIn()
	{
		$this->post('/config/update-items', [],
			['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->assertResponseStatus(401);
	}

	public function testConfigureItemsUpdate()
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

		auth()->login($this->user);

		$this->post('/config/update-items', [
				'items'        => '34',
				'buyRaw'         => true,
				'buyRecycled'    => true,
				'buyRefined'     => true,
				'buyModifier'    => 0.9,
				'buyPrice'       => 5.0,
				'sell'           => true,
				'sellModifier'   => 1.1,
				'sellPrice'      => 6.0,
				'lockPrices'     => true,
			], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->seeJson(['result' => true])
			->seeInDatabase('buyback_items', [
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

		$this->post('/config/update-items', [
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
			], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->seeJson(['result' => true])
			->seeInDatabase('buyback_items', [
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
			])
			->seeInDatabase('buyback_items', [
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

		auth()->logout();

		$this->post('/config/update-items', [],
			['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->assertResponseStatus(401);
	}
}
