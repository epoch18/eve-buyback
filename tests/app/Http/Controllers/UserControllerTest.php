<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var App\EveOnline\SSO
	 */
	private $sso;

	public function setUp()
	{
		parent::setUp();

		$this->sso = Mockery::mock(App\EveOnline\SSO::class);
		$this->app->instance(\App\EveOnline\SSO::class, $this->sso);
	}

	public function testLoginRouteWhileLoggedIn()
	{
		Auth::shouldReceive('check')
		     ->once()
		     ->andReturn(true);

		$this->get('/user/login');

		$this->assertResponseStatus(302);

		$this->assertRedirectedToRoute('index');
	}

	public function testLoginRouteWhileLoggedOut()
	{
		$user = factory(App\Models\User::class)->make();
		$user->save();

		$this->sso->shouldReceive('isReferredByEveOnline')
		          ->once()
		          ->andReturn(true);

		$this->sso->shouldReceive('getAuthenticatedUser')
		          ->once()
		          ->andReturn($user);

		$this->get('/user/login');

		$this->assertResponseStatus(302);

		$this->assertRedirectedToRoute('index');

		$this->assertSessionHas('success');

		$this->assertNotEquals(null, auth()->user()->userID);
	}

	public function testLoginRouteWhileLoggedOutWithErrors()
	{
		$user = factory(App\Models\User::class)->make();
		$user->save();

		$this->sso->shouldReceive('isReferredByEveOnline')
		          ->once()
		          ->andReturn(true);

		$this->sso->shouldReceive('getAuthenticatedUser')
		          ->once()
		          ->andReturn(false);

		$this->get('/user/login');

		$this->assertResponseStatus(302);

		$this->assertRedirectedToRoute('index');

		$this->assertSessionHas('errors');

		$this->assertEquals(null, auth()->user());
	}

	public function testLogoutRoute()
	{
		$this->get('/user/logout');

		$this->assertResponseStatus(302);

		$this->assertRedirectedToRoute('index');

		$this->assertSessionHas('success');

		$this->assertEquals(null, auth()->user());
	}
}
