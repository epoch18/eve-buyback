<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
	/**
	 * @var App\EveOnline\SSO
	 */
	public $sso;

	public function setUp()
	{
		parent::setUp();

		Artisan::call('migrate');
		Artisan::call('db:seed');

		$this->sso = Mockery::mock(App\EveOnline\SSO::class);
	}

	public function testLoginRouteWhileLoggedIn()
	{
		Auth::shouldReceive('check')
		     ->once()
		     ->andReturn(true);

		$this->get('/user/login');

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

		$this->app->instance(\App\EveOnline\SSO::class, $this->sso);

		$this->get('/user/login');

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

		$this->app->instance(\App\EveOnline\SSO::class, $this->sso);

		$this->get('/user/login');

		$this->assertRedirectedToRoute('index');

		$this->assertSessionHas('errors');
	}

	public function testLogoutRoute()
	{
		$this->get('/user/logout');

		$this->assertRedirectedToRoute('index');

		$this->assertSessionHas('success');

		$this->assertEquals(null, auth()->user());
	}
}
