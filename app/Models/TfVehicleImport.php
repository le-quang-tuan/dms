<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent class to describe the tf_vehicle_import table
 *
 * automatically generated by ModelGenerator.php
 */
class TfVehicleImport extends Model
{
    protected $table = 'tf_vehicle_import';

    protected $fillable = array('tenement_id', 'flat_id', 'vehicle_type_id', 'flat_code', 'number_plate', 'name', 'label', 'maker', 'color', 'begin_contract_date', 'end_contract_date', 'driver', 'comment', 'token', 'activation', 'created_by', 'updated_by');
}

