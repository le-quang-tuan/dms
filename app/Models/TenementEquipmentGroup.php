<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent class to describe the tenement_equipment_groups table
 *
 * automatically generated by ModelGenerator.php
 */
class TenementEquipmentGroup extends Model
{
    protected $table = 'tenement_equipment_groups';

    protected $fillable = array('tenement_id', 'equipment_group_code', 'name', 'comment', 'activation', 'created_by',
        'updated_by');

}

