<?php

namespace App\EveOnline;

use \App\Models\User;
use \GuzzleHttp\Client as GuzzleClient;
use \Illuminate\Http\Request;

/**
 * Handles authenticating a user using the eve online single sign on application.
 */
class SSO
{
	/**
	* @var GuzzleHttp\Client $guzzle
	*/
	private $guzzle;

	/**
	* @var Illuminate\Http\Request $request
	*/
	private $request;

	/**
	* @var App\Models\User $user
	*/
	private $user;

	/**
	* Constructs the class.
	* @param  GuzzleHttp\Client       $guzzle
	* @param  Illuminate\Http\Request $request
	* @param  App\Models\User         $user
	*/
	public function __construct(GuzzleClient $guzzle, Request $request, User $user)
	{
		$this->guzzle  = $guzzle;
		$this->request = $request;
		$this->user    = $user;
	}

	/**
	 * Checks if the referrer is the eve online login page.
	 * @return boolean
	 */
	public function isReferredByEveOnline()
	{
		$referrer = $this->request->header('Referer');

		return preg_match("#^https://login.eveonline.com#", $referrer, $match) == true;
	}

	/**
	 * Redirects the user to the login page.
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function redirectToEveOnline()
	{
		$callback = config('sso.callback');
		$client   = config('sso.client'  );
		$url      = "https://login.eveonline.com/oauth/authorize/?response_type=code&redirect_uri={$callback}&client_id={$client}&scope=&state=";

		return redirect()->to($url, 302);
	}

	/**
	 * Gets the model for the authenticated user.
	 * @return  App\Models\User
	 */
	public function getAuthenticatedUser()
	{
		try {
			$code      = $this->request->input('code');
			$token     = $this->getAccessToken($code);
			$character = $this->getCharacterDetails($token);
			$user      = $this->firstOrCreateUser($character);

		} catch (Exception $e) {
			return false;
		}

		return $user;
	}

	/**
	 * Gets the access token for the authenticated user.
	 * @param  string $code
	 * @return string
	 */
	private function getAccessToken($code)
	{
		$client   = config('sso.client');
		$secret   = config('sso.secret');
		$url      = "https://login.eveonline.com/oauth/token/?grant_type=authorization_code&code={$code}";

		$response = $this->guzzle->request('POST', $url, ['auth' => [$client, $secret]]);
		$response = json_decode($response->getBody(), true);

		return $response['access_token'];
	}

	/**
	 * Gets the character details for the authenticated user.
	 * @param  string $token
	 * @return array
	 */
	private function getCharacterDetails($token)
	{
		$url      = 'https://login.eveonline.com/oauth/verify';

		$response = $this->guzzle->request('GET', $url, ['headers' => ['Authorization' => "Bearer {$token}"]]);
		$response = json_decode($response->getBody(), true);

		return $response;
	}

	/**
	 * Gets the user model for the authenticated user.
	 * @param array $character
	 * @return App\Models\User
	 */
	private function firstOrCreateUser($character)
	{
		return $this->user->firstOrCreate([
			'characterID'        => $character['CharacterID'       ],
			'characterName'      => $character['CharacterName'     ],
			'characterOwnerHash' => $character['CharacterOwnerHash'],
		]);
	}
}
