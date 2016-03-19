<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MotdControllerTest extends TestCase
{
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

		$this->post('/manage/motd/edit', $this->headers)->assertResponseStatus(302);
	}

	public function testMustBeAdministrator()
	{
		$user = auth()->user();
		$user->flags = ~\App\Models\User::ADMINISTRATOR;
		$user->save();

		$this->post('/manage/motd/edit', $this->headers)->assertResponseStatus(401);
	}

	public function testMustBeAjaxRequest()
	{
		$this->post('/manage/motd/edit')->assertResponseStatus(500);
	}

	public function testEditMotd()
	{
		$this->post('/manage/motd/edit', ['text' => 'test message'], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);

		$this->seeInDatabase('buyback_settings', ['key' => 'motd', 'value' => 'test message']);
	}

	public function TestRemoveMotd()
	{
		$this->post('/manage/motd/edit', ['text' => ''], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);

		$this->seeInDatabase('buyback_settings', ['key' => 'motd', 'value' => '']);
	}
}
