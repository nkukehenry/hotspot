<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'slug',
        'status',
        'site_code',
        'digital_sales_balance',
        'cash_sales_balance',
        'customer_fee_fixed',
        'customer_fee_percent',
        'site_fee_fixed',
        'site_fee_percent'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($site) {
            // Generate a unique site code
            $site->site_code = 'WIFI-'.strtoupper(Str::random(8));
            
            if (empty($site->slug)) {
                $site->slug = Str::slug($site->name);
            }
        });
        
        static::updating(function ($site) {
             if (empty($site->slug)) {
                $site->slug = Str::slug($site->name);
            }
        });
    }

    private static function generateUniqueSiteCode()
    {
        do {
            // Generate a random code (you can customize the format)
            $code =  'SITE-'.strtoupper(Str::random(8));
        } while (self::where('site_code', $code)->exists()); // Ensure uniqueness

        return $code;
    }
    
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}