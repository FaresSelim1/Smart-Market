<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

/**
 * Global scope that filters queries by the active branch.
 * Applied to models that should be scoped to a user's selected branch.
 */
class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * Ensures users only see data for their selected branch.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Session::has('active_branch_id')) {
            $builder->where('branch_id', Session::get('active_branch_id'));
        }
    }
}