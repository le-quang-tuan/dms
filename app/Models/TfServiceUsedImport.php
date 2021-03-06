<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent class to describe the tf_service_used_import table
 *
 * automatically generated by ModelGenerator.php
 */
class TfServiceUsedImport extends Model
{
    protected $table = 'tf_service_used_import';

    protected $fillable = array('tenement_id', 'flat_id', 'flat_code', 'year_month', 'name', 'mount',
        'price', 'total', 'comment', 'unit', 'token', 'activation', 'created_by', 'updated_by');

}

