<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'status', 'fee_id'];

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }
}
