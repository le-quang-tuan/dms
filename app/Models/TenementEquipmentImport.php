<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent class to describe the tenement_equipments_import table
 *
 * automatically generated by ModelGenerator.php
 */
class TenementEquipmentImport extends Model
{
    protected $table = 'tenement_equipments_import';

    protected $fillable = array('tenement_id', 'equipment_group_id', 'equipment_type_id', 'equipment_code', 'name','producer_id',
        'product_of', 'label', 'model', 'specification', 'area', 'comment', 'token', 'activation', 'created_by',
        'updated_by');

}
