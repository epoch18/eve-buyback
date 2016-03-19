<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContractControllerTest extends TestCase {
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$user = factory(App\Models\User::class)->make();
		$user->setContractor(true)->save();
		auth()->login($user);
	}

	public function testMustBeLoggedIn()
	{
		auth()->logout();

		$this->get('/contract')->assertResponseStatus(302);
	}

	public function testMustBeContractor()
	{
		$user = auth()->user();
		$user->flags = ~\App\Models\User::CONTRACTOR - \App\Models\User::ADMINISTRATOR;
		$user->save();

		$this->get('/contract')->assertResponseStatus(401);
	}
}
