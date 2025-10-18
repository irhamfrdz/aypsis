<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display audit logs with filtering
     */
    public function index(Request $request)
    {
        $this->authorize('audit-log-view');

        $query = AuditLog::with(['user', 'auditable'])
            ->orderBy('created_at', 'desc');

        // Filter by module
        if ($request->filled('module')) {
            $query->module($request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->action($request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = $request->filled('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : Carbon::now()->endOfDay();

            $query->dateRange($startDate, $endDate);
        }

        // Search in description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('username', 'like', "%{$search}%");
                  });
            });
        }

        $auditLogs = $query->paginate(25);

        // Get filter options
        $modules = AuditLog::distinct('module')->pluck('module')->sort();
        $actions = AuditLog::distinct('action')->pluck('action')->sort();
        $users = User::select('id', 'username')->orderBy('username')->get();

        return view('audit-logs.index', compact('auditLogs', 'modules', 'actions', 'users'));
    }

    /**
     * Show detailed audit log
     */
    public function show($id)
    {
        $this->authorize('audit-log-view');

        $auditLog = AuditLog::with(['user', 'auditable'])->findOrFail($id);

        return view('audit-logs.show', compact('auditLog'));
    }

    /**
     * Get audit logs for specific model (AJAX)
     */
    public function getModelAuditLogs(Request $request)
    {
        // Debug logging
        \Illuminate\Support\Facades\Log::info('getModelAuditLogs called', [
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'user_name' => \Illuminate\Support\Facades\Auth::user()->username ?? 'not_logged_in',
            'model_type' => $request->model_type,
            'model_id' => $request->model_id,
            'has_permission' => \Illuminate\Support\Facades\Auth::user() ? \Illuminate\Support\Facades\Auth::user()->hasPermissionTo('audit-log-view') : false
        ]);

        $this->authorize('audit-log-view');

        $modelType = $request->model_type;
        $modelId = $request->model_id;

        $auditLogs = AuditLog::where('auditable_type', $modelType)
            ->where('auditable_id', $modelId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        \Illuminate\Support\Facades\Log::info('getModelAuditLogs result', [
            'count' => $auditLogs->count()
        ]);

        return response()->json([
            'success' => true,
            'data' => $auditLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'user_name' => $log->getUserDisplayName(),
                    'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                    'changes' => $log->getFormattedChanges()
                ];
            })
        ]);
    }

    /**
     * Export audit logs to CSV
     */
    public function export(Request $request)
    {
        $this->authorize('audit-log-export');

        $query = AuditLog::with(['user', 'auditable'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('module')) {
            $query->module($request->module);
        }
        if ($request->filled('action')) {
            $query->action($request->action);
        }
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = $request->filled('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : Carbon::now()->endOfDay();
            $query->dateRange($startDate, $endDate);
        }

        $auditLogs = $query->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($auditLogs) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, [
                'ID', 'User', 'Module', 'Action', 'Description',
                'Model Type', 'Model ID', 'IP Address', 'Timestamp'
            ], ';');

            // Data
            foreach ($auditLogs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->getUserDisplayName(),
                    $log->module,
                    $log->action,
                    $log->description,
                    class_basename($log->auditable_type),
                    $log->auditable_id,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
