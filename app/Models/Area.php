<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'areas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'branchid', 'activation', 'note'];



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    //protected $hidden = ['remember_token'];
    
    // public function hasManyRoomtype() {        
    //     return $this->hasMany('App\Models\Roomtype');        
    // }
    
    public function company() {        
        return $this->hasMany('App\Model\Company', 'areaid');
    }
}
