<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSiteScope;

class Voucher extends Model
{
    use HasSiteScope;

    protected $fillable = ['code', 'package_id', 'is_used', 'site_id'];

    public function markAsUsed()
    {
        $this->is_used = true;
        $this->save();
    }


    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
