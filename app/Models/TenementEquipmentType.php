<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent class to describe the tenement_equipment_types table
 *
 * automatically generated by ModelGenerator.php
 */
class TenementEquipmentType extends Model
{
    protected $table = 'tenement_equipment_types';

    protected $fillable = array('tenement_id', 'equipment_name', 'comment', 'activation', 'created_by', 'updated_by');

}
