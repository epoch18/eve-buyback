<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => env('MAILGUN_DOMAIN'),
		'secret' => env('MAILGUN_SECRET'),
	],

	'mandrill' => [
		'secret' => env('MANDRILL_SECRET'),
	],

	'ses' => [
		'key'	 => env('SES_KEY'),
		'secret' => env('SES_SECRET'),
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'	 => App\User::class,
		'key'	 => env('STRIPE_KEY'),
		'secret' => env('STRIPE_SECRET'),
	],

	'eveapi' => [
		'keyid' => env('EVE_API_KEYID', ''),
		'vcode' => env('EVE_API_VCODE', ''),
	],

	'evecentral' => [
		'url'       => env('EVE_CENTRAL_URL'      , ''),
		'usesystem' => env('EVE_CENTRAL_USESYSTEM', ''),
		'minq'      => env('EVE_CENTRAL_MINQ'     , ''),
		'buy'       => env('EVE_CENTRAL_BUY'      , ''),
		'sell'      => env('EVE_CENTRAL_SELL'     , ''),
	],

	'evesso' => [
		'client'   => env('EVE_SSO_CLIENT_ID'   , ''),
		'secret'   => env('EVE_SSO_SECRET_KEY'  , ''),
		'callback' => env('EVE_SSO_CALLBACK_URL', ''),
	],

];
