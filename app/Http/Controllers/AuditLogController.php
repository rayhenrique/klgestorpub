<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->has('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->has('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $logs = $query->paginate(20);

        // Preparar dados para os filtros
        $modelTypes = AuditLog::distinct()->pluck('model_type');
        $users = \App\Models\User::all();
        
        return view('audit.index', compact('logs', 'modelTypes', 'users'));
    }

    public function show(AuditLog $log)
    {
        return view('audit.show', compact('log'));
    }
}
