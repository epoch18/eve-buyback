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
				['key'   => 'motd', 'value' => 'test message']);

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
}
