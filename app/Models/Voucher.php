<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['code', 'package_id', 'is_used'];

    public function markAsUsed()
    {
        $this->is_used = true;
        $this->save();
    }


    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
