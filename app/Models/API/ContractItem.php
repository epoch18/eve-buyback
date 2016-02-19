<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class ContractItem extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'api_contract_items';

	/**
	 * The primary key.
	 *
	 * @var string
	 */
	protected $primaryKey = 'recordID';

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
		'recordID',
		'contractID',
		'typeID',
		'quantity',
		'rawQuantity',
		'singleton',
		'included',
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
		'recordID'    => 'integer',
		'contractID'  => 'integer',
		'typeID'      => 'integer',
		'quantity'    => 'integer',
		'rawQuantity' => 'integer',
		'singleton'   => 'boolean',
		'included'    => 'boolean',
	];
}
