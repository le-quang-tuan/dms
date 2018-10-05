<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenementElecTariff extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'tenement_elec_tariff';

    protected $fillable = array('elec_type_id', 'name', 'index_from', 'price', 'other_fee01', 'other_fee02', 'vat',
        'comment', 'activation', 'created_by', 'updated_by');

}

