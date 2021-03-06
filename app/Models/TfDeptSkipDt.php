<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent class to describe the tf_paid_dt table
 *
 * automatically generated by ModelGenerator.php
 */
class TfDeptSkipDt extends Model
{
    protected $table = 'tf_dept_skip_dt';

    protected $fillable = array('dept_skip_id', 'payment_type', 'year_month', 'money', 'prepaid_flg', 'comment',
        'activation', 'created_by', 'updated_by');

}

