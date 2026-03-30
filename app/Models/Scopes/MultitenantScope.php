<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MultitenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Owners see everything.
            if ($user->hasRole('Owner')) {
                return;
            }

            $table = $model->getTable();

            // 1. Handle Company Admin Scoping
            if ($user->hasRole('Company Admin')) {
                if (Schema::hasColumn($table, 'company_id')) {
                    $builder->where($table . '.company_id', $user->company_id);
                } elseif (Schema::hasColumn($table, 'site_id')) {
                    // For models related to a site, ensure the site belongs to the company.
                    $builder->whereIn($table . '.site_id', function ($query) use ($user) {
                        $query->select('id')->from('sites')->where('company_id', $user->company_id);
                    });
                }
                return;
            }

            // 2. Handle Site-restricted Roles (Manager/Supervisor/Agent)
            if (Schema::hasColumn($table, 'site_id')) {
                if ($user->site_id) {
                    $builder->where($table . '.site_id', $user->site_id);
                } else {
                    // Force zero results if they HAVE to be in a site but aren't assigned.
                    $builder->whereRaw('0 = 1');
                }
            } elseif (Schema::hasColumn($table, 'company_id')) {
                // For models like User/Site that have company_id, lower roles still stay in their company.
                $builder->where($table . '.company_id', $user->company_id);
            }
        }
    }
}
