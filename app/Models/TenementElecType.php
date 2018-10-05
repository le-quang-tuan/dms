<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenementElecType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tenement_elec_types';

    protected $fillable = array('tenement_id', 'elec_type', 'elec_code', 'comment', 'activation', 'created_by', 'updated_by');
}