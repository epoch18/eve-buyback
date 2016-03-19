<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageControllerTest extends TestCase {
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$user = factory(App\Models\User::class)->make();
		$user->setAdministrator(true)->save();
		auth()->login($user);
	}

	public function testMustBeLoggedIn()
	{
		auth()->logout();

		$this->get('/manage')->assertResponseStatus(302);
	}

	public function testMustBeAdministrator()
	{
		$user = auth()->user();
		$user->flags = ~\App\Models\User::ADMINISTRATOR;
		$user->save();

		$this->get('/manage')->assertResponseStatus(401);
	}
}
