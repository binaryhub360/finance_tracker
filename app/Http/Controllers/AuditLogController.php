<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs with filters and KPI metrics.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        // Base query for user's audit logs
        $query = AuditLog::query()
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereNull('user_id');
            });

        // KPI metrics computed before applying interactive filters
        $stats = [
            'total_events' => (clone $query)->count(),
            'events_today' => (clone $query)->whereDate('created_at', Carbon::today())->count(),
            'total_updates' => (clone $query)->where('action', 'updated')->count(),
            'total_deletions' => (clone $query)->where('action', 'deleted')->count(),
        ];

        // Apply filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $entityClass = $request->entity_type;
            if (!str_contains($entityClass, '\\')) {
                $entityClass = 'App\\Models\\' . $entityClass;
            }
            $query->where('auditable_type', $entityClass);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                  ->orWhere('entity_label', 'like', $search)
                  ->orWhere('ip_address', 'like', $search);
            });
        }

        $logs = $query->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        // Get distinct actions & entity types for dropdown filters
        $actions = AuditLog::select('action')
            ->distinct()
            ->pluck('action')
            ->toArray();

        $entityTypes = AuditLog::select('auditable_type')
            ->whereNotNull('auditable_type')
            ->distinct()
            ->pluck('auditable_type')
            ->map(fn($type) => class_basename($type))
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return view('audit-logs.index', compact(
            'logs',
            'stats',
            'actions',
            'entityTypes'
        ));
    }

    /**
     * Show audit log details / diff as JSON.
     */
    public function show(AuditLog $auditLog): JsonResponse
    {
        return response()->json([
            'id' => $auditLog->id,
            'action' => $auditLog->action,
            'action_badge' => $auditLog->action_badge,
            'entity_type' => $auditLog->entity_type_name,
            'entity_label' => $auditLog->entity_label,
            'description' => $auditLog->description,
            'user' => $auditLog->user?->name ?? 'System',
            'ip_address' => $auditLog->ip_address,
            'user_agent' => $auditLog->user_agent,
            'created_at' => $auditLog->created_at?->format('M d, Y h:i:s A'),
            'diff' => $auditLog->diff,
            'old_values' => $auditLog->old_values,
            'new_values' => $auditLog->new_values,
        ]);
    }

    /**
     * Export the filtered audit logs to CSV.
     */
    public function exportCsv(Request $request): Response
    {
        $userId = $request->user()->id;

        $query = AuditLog::query()
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereNull('user_id');
            });

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $entityClass = $request->entity_type;
            if (!str_contains($entityClass, '\\')) {
                $entityClass = 'App\\Models\\' . $entityClass;
            }
            $query->where('auditable_type', $entityClass);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                  ->orWhere('entity_label', 'like', $search)
                  ->orWhere('ip_address', 'like', $search);
            });
        }

        $logs = $query->orderByDesc('created_at')
            ->orderByDesc('id')
            ->with('user')
            ->limit(5000)
            ->get();

        $rows = [
            [
                'Log ID',
                'Timestamp (UTC/Server)',
                'User',
                'Action',
                'Entity Type',
                'Entity Reference',
                'Description',
                'IP Address',
                'Changed Fields',
                'Old Values (JSON)',
                'New Values (JSON)',
            ]
        ];

        foreach ($logs as $log) {
            $diffKeys = !empty($log->diff) ? implode(', ', array_keys($log->diff)) : '-';
            $rows[] = [
                'AUD-' . str_pad($log->id, 6, '0', STR_PAD_LEFT),
                $log->created_at?->format('Y-m-d H:i:s') ?? '',
                $log->user?->name ?? 'System',
                strtoupper($log->action),
                $log->entity_type_name,
                $log->entity_label ?? 'N/A',
                $log->description,
                $log->ip_address ?? '',
                $diffKeys,
                $log->old_values ? json_encode($log->old_values, JSON_UNESCAPED_SLASHES) : '',
                $log->new_values ? json_encode($log->new_values, JSON_UNESCAPED_SLASHES) : '',
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($field) {
                $field = str_replace('"', '""', (string) $field);
                return '"' . $field . '"';
            }, $row)) . "\n";
        }

        $filename = "Audit_Trail_" . Carbon::now()->format('Ymd_His') . ".csv";

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

