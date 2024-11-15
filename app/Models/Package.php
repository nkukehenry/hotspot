<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CodeGenerator;

class Package extends Model
{
    protected $fillable = ['name', 'cost', 'description', 'location_id','code'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function getIconAttribute($value)
    {
        return $value ? asset('images/' . $value) : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            $package->code = CodeGenerator::generateUniqueCode(self::class);
        });
    }
}
