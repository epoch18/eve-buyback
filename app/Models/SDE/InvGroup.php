<?php

namespace App\Models\SDE;

use Illuminate\Database\Eloquent\Model;

class InvGroup extends Model
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
	protected $table = 'invGroups';

	/**
	 * The primary key used by the model.
	 * @var string
	 */
	protected $primaryKey = 'groupID';

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

	public function category() {
		return $this->hasOne(\App\Models\SDE\InvCategory::class, 'categoryID', 'categoryID'); }

	public function types() {
		return $this->hasMany(\App\Models\SDE\InvType::class, 'groupID', 'groupID'); }
}
