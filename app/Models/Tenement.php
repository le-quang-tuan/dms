<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenement extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tenements';
    
    protected $fillable = array('tenement_code', 'name', 'address', 'manager_fee', 'loss_avg', 'bank', 'account',
        'account_name', 'office', 'office_address', 'office_phone', 'parkingfee_calulate_type', 'loss_avg_elec',
        'managerfee_calculate_type', 'loss_avg_gas', 'gas_unit', 'paid_bill_text', 'paid_bill_logo', 'payment_notice_text',
        'payment_notice_logo', 'mMonthView', 'p_month_view', 'w_month_view', 'e_month_view', 's_month_view', 'g_month_view',

        'bill_sign_image', 'comment', 'activation', 'created_by', 'updated_by', 'company_id', 'paid_bill_text', 'contact', 'managerment','caption1','caption2','caption3','branch','manager_company');

    public function flats() {        
        return $this->hasMany('App\Model\Flat', 'tenement_id');
    }
}
