<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CodeGenerator;

use App\Traits\HasSiteScope;

class Package extends Model
{
    use HasSiteScope;

    protected $fillable = ['name', 'cost', 'description', 'site_id','code'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
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
