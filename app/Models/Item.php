<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'buyback_items';

	/**
	 * The primary key.
	 *
	 * @var string
	 */
	protected $primaryKey = 'typeID';

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
		'typeID',
		'typeName',
		'buyRaw',
		'buyRecycled',
		'buyRefined',
		'buyModifier',
		'buyPrice',
		'sell',
		'sellModifier',
		'sellPrice',
		'lockPrices',
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
		'typeID'       => 'integer',
		'typeName'     => 'string',
		'buyRaw'       => 'boolean',
		'buyRecycled'  => 'boolean',
		'buyRefined'   => 'boolean',
		'buyModifier'  => 'double',
		'buyPrice'     => 'double',
		'sell'         => 'boolean',
		'sellModifier' => 'double',
		'sellPrice'    => 'double',
		'lockPrices'   => 'boolean',
	];

	public function type()
	{
		return $this->hasOne(\App\Models\SDE\InvType::class, 'typeID', 'typeID');
	}
}
