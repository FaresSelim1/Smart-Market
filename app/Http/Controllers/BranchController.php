<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BranchController extends Controller
{
    public function select(Request $request)
    {
        $request->validate(['branch_id' => 'required|exists:branches,id']);
        
        Session::put('active_branch_id', $request->branch_id);
        
        return back()->with('status', 'Branch switched successfully.');
    }
}