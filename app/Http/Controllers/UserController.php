<?php

namespace App\Http\Controllers;

use \App\EveOnline\SSO;
use \Illuminate\Http\Request;

class UserController extends Controller
{
	/**
	* @var App\EveOnline\SSO
	*/
	private $sso;

	/**
	* Constructs the class.
	* @param  Illuminate\Http\Request $request
	* @param  App\EveOnline\SSO       $sso
	*/
	public function __construct(Request $request, SSO $sso)
	{
		$this->request = $request;
		$this->sso     = $sso;
	}

	/**
	 * Handles logging in a user.
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function login() {
		if (auth()->check()) {
			return redirect()->route('index');
		}

		if (!$this->sso->isReferredByEveOnline()) {
			return $this->sso->redirectToEveOnline();
		}

		$user = $this->sso->getAuthenticatedUser();

		if (!$user) {
			return redirect()->route('index')
			                 ->withErrors([trans('messages.login_failed')]);
		}

		auth()->loginUsingId($user->userID, true);

		return redirect()->route('index')
			             ->withSuccess(trans('messages.login_success'));
	}

	/**
	 * Handles logging out a user.
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function logout() {
		auth()->logout();

		return redirect()->route('index')
			             ->withSuccess(trans('messages.logout_success'));
	}
}
