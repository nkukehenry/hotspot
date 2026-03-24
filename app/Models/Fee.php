<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = [
        'name',
        'customer_fee_fixed',
        'customer_fee_percent',
        'site_fee_fixed',
        'site_fee_percent',
        'status'
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
