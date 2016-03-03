<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
	}

	public function testAdministratorFlag()
	{
		$user = new \App\Models\User;

		$user->setAdministrator(false);

		$this->assertEquals(false, $user->isAdministrator());

		$user->setAdministrator(true);

		$this->assertEquals(true, $user->isAdministrator());

		$user->setAdministrator(false);

		$this->assertEquals(false, $user->isAdministrator());
	}

	public function testContractorFlag()
	{
		$user = new \App\Models\User;

		$user->setContractor(false);

		$this->assertEquals(false, $user->isContractor());

		$user->setContractor(true);

		$this->assertEquals(true, $user->isContractor());

		$user->setContractor(false);

		$this->assertEquals(false, $user->isContractor());
	}
}
