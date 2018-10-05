<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'occupations';
	
	protected $guarded = ['id', 'created_at', 'updated_at'];

	public $incrementing = false;


	/************************************************************
     * RELATION SHIP
     */
	public function users()
	{
		return $this->hasMany('App\Models\User');
	}
}
