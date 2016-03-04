<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Outpost extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'api_outposts';

	/**
	 * The primary key.
	 *
	 * @var string
	 */
	protected $primaryKey = 'stationID';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'stationID',
		'stationName',
		'stationTypeID',
		'solarSystemID',
		'corporationID',
		'corporationName',
		'x',
		'y',
		'z',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'stationID'       => 'integer',
		'stationName'     => 'string',
		'stationTypeID'   => 'integer',
		'solarSystemID'   => 'integer',
		'corporationID'   => 'integer',
		'corporationName' => 'string',
		'x'               => 'integer',
		'y'               => 'integer',
		'z'               => 'integer',
	];

	public function solarSystem()
	{
		return $this->belongsTo(\App\Models\SDE\mapSolarSystem::class, 'solarSystemID', 'solarSystemID');
	}
}
