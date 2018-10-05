<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'companyinfo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'address', 'contactname', 'tel', 'fax', 'contractrate', 'companyid', 'areaid', 'activation', 'note'];    
    
    public function area(){
        return $this->belongsTo('App\Model\Area', 'areaid');
    }

    // public function hasManyCustomer() {        
    //     return $this->hasMany('App\Models\Customer');
        
    // }
}
