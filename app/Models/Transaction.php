<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = ['voucher_id', 'transaction_id', 'mobile_number', 'amount', 'status', 'package_id'];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

}
