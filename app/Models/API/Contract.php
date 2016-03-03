<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'api_contracts';

	/**
	 * The primary key.
	 *
	 * @var string
	 */
	protected $primaryKey = 'contractID';

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
		'contractID',
		'issuerID',
		'issuerCorpID',
		'assigneeID',
		'acceptorID',
		'startStationID',
		'endStationID',
		'type',
		'status',
		'title',
		'forCorp',
		'availability',
		'dateIssued',
		'dateExpired',
		'dateAccepted',
		'numDays',
		'dateCompleted',
		'price',
		'reward',
		'collateral',
		'buyout',
		'volume',
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
		'contractID'     => 'integer',
		'issuerID'       => 'integer',
		'issuerCorpID'   => 'integer',
		'assigneeID'     => 'integer',
		'acceptorID'     => 'integer',
		'startStationID' => 'integer',
		'endStationID'   => 'integer',
		'type'           => 'string',
		'status'         => 'string',
		'title'          => 'string',
		'forCorp'        => 'boolean',
		'availability'   => 'integer',
		'dateIssued'     => 'datetime',
		'dateExpired'    => 'datetime',
		'dateAccepted'   => 'datetime',
		'numDays'        => 'integer',
		'dateCompleted'  => 'datetime',
		'price'          => 'double',
		'reward'         => 'double',
		'collateral'     => 'double',
		'buyout'         => 'double',
		'volume'         => 'double',
	];

	public function items()
	{
		return $this->hasMany(\App\Models\API\ContractItem::class, 'contractID', 'contractID');
	}
}
