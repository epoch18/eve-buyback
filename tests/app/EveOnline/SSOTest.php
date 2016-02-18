<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SSOTest extends TestCase
{
	use DatabaseTransactions;

	/**
	 * @var GuzzleHttp\Client
	 */
	public $guzzle;

	/**
	 * @var Illuminate\Http\Request
	 */
	public $request;

	/**
	 * @var App\Models\User
	 */
	public $user;

	public function setUp()
	{
		parent::setUp();

		$this->guzzle  = Mockery::mock(\GuzzleHttp\Client::class);
		$this->request = Mockery::mock(\Illuminate\Http\Request::class);
		$this->user    = Mockery::mock(\App\Models\User::class);
	}

	public function testIsReferredByEveOnline()
	{
		$this->request->shouldReceive('header')
		              ->with('Referer')
		              ->once()
		              ->andReturn('https://login.eveonline.com');

		$sso = new \App\EveOnline\SSO($this->guzzle, $this->request, $this->user);

		$this->assertEquals(true, $sso->isReferredByEveOnline());
	}

	public function testNotReferredByEveOnline()
	{
		$this->request->shouldReceive('header')
		              ->with('Referer')
		              ->once()
		              ->andReturn('https://localhost');

		$sso = new \App\EveOnline\SSO($this->guzzle, $this->request, $this->user);

		$this->assertEquals(false, $sso->isReferredByEveOnline());
	}

	public function testRedirectToEveOnline()
	{
		$sso      = new \App\EveOnline\SSO($this->guzzle, $this->request, $this->user);
		$response = $sso->redirectToEveOnline();

		$this->assertInstanceOf(Illuminate\Http\RedirectResponse::class, $response);

		$this->assertStringStartsWith('https://login.eveonline.com/oauth/authorize', $response->headers->get('location'));
	}

	public function testGetAuthenticatedUser()
	{
		// request
		$this->request->shouldReceive('input')
		              ->once()
		              ->andReturn('');

		// getAccessToken
		$response1 = Mockery::mock(stdClass::class);
		$response1->shouldReceive('getBody')
		          ->once()
		          ->andReturn('{ "access_token": "token", "token_type": "Bearer", "expires_in": 300, "refresh_token": null }');

		$this->guzzle->shouldReceive('request')
		             ->once()
		             ->andReturn($response1);

		// getCharacterDetails
		$response2 = Mockery::mock(stdClass::class);
		$response2->shouldReceive('getBody')
		          ->once()
		          ->andReturn('{ "CharacterID": 273042051, "CharacterName": "CCP illurkall", "ExpiresOn": "2014-05-23T15:01:15.182864Z", "Scopes": " ", "TokenType": "Character", "CharacterOwnerHash": "XM4D...FoY=" }');

		$this->guzzle->shouldReceive('request')
		             ->once()
		             ->andReturn($response2);

		// test
		$sso  = new \App\EveOnline\SSO($this->guzzle, $this->request, new \App\Models\User);
		$user = $sso->getAuthenticatedUser();

		$this->assertEquals(273042051      , $user->characterID);
		$this->assertEquals('CCP illurkall', $user->characterName);
		$this->assertEquals('XM4D...FoY='  , $user->characterOwnerHash);
	}
}
