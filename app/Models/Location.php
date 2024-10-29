<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'location_code', // Ensure location_code is fillable
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($location) {
            // Generate a unique location code
            $location->location_code = self::generateUniqueLocationCode();
        });
    }

    private static function generateUniqueLocationCode()
    {
        do {
            // Generate a random code (you can customize the format)
            $code =  'WIFI-'.strtoupper(Str::random(8));
        } while (self::where('location_code', $code)->exists()); // Ensure uniqueness

        return $code;
    }
}