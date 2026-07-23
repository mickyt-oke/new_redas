<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    /**
     * Display the audit trail with filters.
     */
    public function index(Request $request)
    {
        $query = AuditLog::query()->with('user')->latest('created_at');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->input('entity_type'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereRaw("JSON_EXTRACT(details, '$') LIKE ?", ["%{$search}%"]);
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $actions = AuditLog::query()
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->pluck('action');

        $entityTypes = AuditLog::query()
            ->whereNotNull('entity_type')
            ->selectRaw('entity_type, COUNT(*) as count')
            ->groupBy('entity_type')
            ->orderByDesc('count')
            ->pluck('entity_type');

        return view('admin.audit-log.index', [
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'entityTypes' => $entityTypes,
            'filters' => $request->only([
                'date_from',
                'date_to',
                'user_id',
                'action',
                'status',
                'entity_type',
                'search',
            ]),
        ]);
    }
}
