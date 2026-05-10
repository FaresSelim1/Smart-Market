<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HandleBranchContext
{
    public function handle(Request $request, Closure $next)
    {
        // If the request has a branch header (API) or session (Web), set the global ID
        if ($request->hasHeader('X-Branch-Id')) {
            Session::put('active_branch_id', $request->header('X-Branch-Id'));
        }

        return $next($request);
    }
}