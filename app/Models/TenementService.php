<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Eloquent class to describe the tenement_services table
 *
 * automatically generated by ModelGenerator.php
 */
class TenementService extends Model
{
    protected $table = 'tenement_services';

    protected $fillable = array('service','service_type_id','price','manage_by','note','comment','activation','created_at','updated_at','created_by','updated_by');
}

