<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class SiteScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Skip scope for Owners and Company Admins as they see higher level data.
            if ($user->hasRole('Owner') || $user->hasRole('Company Admin')) {
                return;
            }

            // For all other roles, they MUST be scoped to a site.
            $table = $model->getTable();
            
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'site_id')) {
                if ($user->site_id) {
                    $builder->where($table . '.site_id', $user->site_id);
                } else {
                    // Force a condition that fails if they don't have a site_id
                    $builder->whereRaw('0 = 1');
                }
            }
        }
    }
}
