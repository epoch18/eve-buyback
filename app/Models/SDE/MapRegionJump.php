<?php

namespace App\Models\SDE;

use Illuminate\Database\Eloquent\Model;

class MapRegionJump extends Model
{
	/**
	 * The database connection used by the model.
	 * @var string
	 */
	protected $connection = 'evesde';

	/**
	 * The database table used by the model.
	 * @var string
	 */
	protected $table = 'mapRegionJumps';

	/**
	 * The attributes that are mass assignable.
	 * @var array
	 */
	protected $fillable = [];

	/**
	 * The attributes excluded from the model's JSON form.
	 * @var array
	 */
	protected $hidden = [];

	/**
	 * The attributes that should be casted to native types.
	 * @var array
	 */
	protected $casts = [];

	/**
	 * Indicates if the model has an incrementing primary key.
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * Indicates if the model should be timestamped.
	 * @var bool
	 */
	public $timestamps = false;
}
