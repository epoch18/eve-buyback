<?php

namespace App\Models\SDE;

use Illuminate\Database\Eloquent\Model;

class InvCategory extends Model
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
	protected $table = 'invCategories';

	/**
	 * The primary key used by the model.
	 * @var string
	 */
	protected $primaryKey = 'categoryID';

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

	public function groups() {
		return $this->hasMany(\App\Models\SDE\InvGroup::class, 'categoryID', 'categoryID'); }

	public function types() {
		return $this->hasManyThrough(\App\Models\SDE\InvType::class, \App\Models\SDE\InvGroup::class, 'categoryID', 'groupID'); }
}
