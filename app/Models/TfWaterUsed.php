<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent class to describe the tf_water_used table
 *
 * automatically generated by ModelGenerator.php
 */
class TfWaterUsed extends Model
{
    protected $table = 'tf_water_used';

    protected $fillable = array('flat_id', 'year_month', 'date_from', 'date_to', 'old_index', 'new_index', 'comment', 'activation', 'created_by', 'updated_by','used_deduct', 'prev_year_month_price');

}

