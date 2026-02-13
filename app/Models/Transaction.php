<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSiteScope;

class Transaction extends Model
{
    use HasSiteScope;

    protected $fillable = [
        'voucher_id', 
        'transaction_id', 
        'mobile_number', 
        'amount', 
        'status', 
        'package_id', 
        'site_id', 
        'agent_id',
        'customer_fee',
        'site_fee',
        'total_fee',
        'total_amount',
        'fee_distributed'
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
    
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
