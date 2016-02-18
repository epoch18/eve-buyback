<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	/**
	 * The primary key.
	 *
	 * @var string
	 */
	protected $primaryKey = 'userID';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'characterID',
		'characterName',
		'characterOwnerHash',
		'corporationID',
		'corporationName',
		'corporationTicker',
		'allianceID',
		'allianceName',
		'allianceTicker',
		'flags',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'characterOwnerHash', 'flags', 'remember_token',
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'userID'             => 'integer',
		'characterID'        => 'integer',
		'characterName'      => 'string',
		'characterOwnerHash' => 'string',
		'corporationID'      => 'integer',
		'corporationName'    => 'string',
		'corporationTicker'  => 'string',
		'allianceID'         => 'integer',
		'allianceName'       => 'string',
		'allianceTicker'     => 'string',
		'flags'              => 'integer',
	];
}
