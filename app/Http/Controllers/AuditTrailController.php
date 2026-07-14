<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->input('action');
        $date = $request->input('date');

        $query = AuditTrail::with('user')->latest('created_at');

        if ($action) {
            $query->where('action', $action);
        }
        
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $auditTrails = $query->paginate(20)->withQueryString();

        return view('audit-trails.index', compact('auditTrails', 'action', 'date'));
    }

    public function show(AuditTrail $auditTrail)
    {
        $auditTrail->load('user');
        return view('audit-trails.show', compact('auditTrail'));
    }
}
