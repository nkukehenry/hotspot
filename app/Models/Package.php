<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'cost', 'description', 'location_id'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function getIconAttribute($value)
    {
        return $value ? asset('images/' . $value) : null;
    }
}
