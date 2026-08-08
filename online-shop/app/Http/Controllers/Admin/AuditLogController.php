<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user');

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . trim($request->action) . '%');
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . trim($request->search) . '%');
        }

        $logs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $modules = AuditLog::distinct()->orderBy('module')->pluck('module');

        return view('admin.audit-logs.index', compact('logs', 'modules'));
    }
}
