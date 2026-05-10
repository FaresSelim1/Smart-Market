<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SetBranchContext
{
    public function handle(Request $request, Closure $next)
    {
        // If the user selects a branch from a dropdown or URL, store it in session
        if ($request->has('branch_id')) {
            Session::put('active_branch_id', $request->branch_id);
        }

        return $next($request);
    }
}