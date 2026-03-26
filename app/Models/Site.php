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
        'site_fee_percent',
        'logo',
        'contact_email',
        'contact_phone',
        'settlement_momo_number',
        'settlement_account_name',
        'company_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function settlementRequests()
    {
        return $this->hasMany(SettlementRequest::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($site) {
            // Generate a unique site code
            if (empty($site->site_code)) {
                $site->site_code = self::generateUniqueSiteCode();
            }
            
            if (empty($site->slug)) {
                $site->slug = self::generateUniqueSlug($site->name);
            }
        });
        
        static::updating(function ($site) {
             if ($site->isDirty('name') && empty($site->slug)) {
                $site->slug = self::generateUniqueSlug($site->name);
            }
        });
    }

    private static function generateUniqueSlug($name)
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (self::where('slug', $slug)->exists()) {
            $count++;
            $slug = "{$originalSlug}-{$count}";
        }

        return $slug;
    }

    private static function generateUniqueSiteCode()
    {
        do {
            $code = 'WIFI-' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (self::where('site_code', $code)->exists());

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