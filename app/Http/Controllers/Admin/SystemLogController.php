<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SystemLogController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => SystemLog::count(),
            'today' => SystemLog::whereDate('created_at', Carbon::today())->count(),
            'this_week' => SystemLog::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'this_month' => SystemLog::whereMonth('created_at', Carbon::now()->month)->count(),
            'last_month' => SystemLog::whereMonth('created_at', Carbon::now()->subMonth()->month)->count(),
        ];

        $actions = SystemLog::select('action')->distinct()->pluck('action');

        return view('admin.logs.index', compact('stats', 'actions'));
    }

    public function getLogsData(Request $request)
    {
        $query = SystemLog::with('admin')->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%")
                    ->orWhereHas('admin', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('action') && !empty($request->action) && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage);

        $actionNames = $this->getActionNames();
        $actionIcons = $this->getActionIcons();
        $actionColors = $this->getActionColors();

        $logs->getCollection()->transform(function ($log) use ($actionNames, $actionIcons, $actionColors) {
            $log->action_display = $actionNames[$log->action] ?? ucfirst(str_replace('_', ' ', $log->action));
            $log->action_icon = $actionIcons[$log->action] ?? 'fa-history';
            $log->action_color = $actionColors[$log->action] ?? 'secondary';
            $log->details_short = $this->formatDetailsShort($log->details);
            $log->details_html = $this->formatDetailsHtml($log->details);
            return $log;
        });

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'total' => $logs->total(),
            'per_page' => $logs->perPage(),
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
        ]);
    }

    public function destroy($id)
    {
        $log = SystemLog::findOrFail($id);
        $log->delete();

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'delete_log',
            'details' => [
                'deleted_log_id' => $log->id,
                'deleted_log_action' => $log->action,
                'deleted_at' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Log entry deleted successfully'),
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['log_ids' => 'required|array', 'log_ids.*' => 'exists:system_logs,id']);
        $count = SystemLog::whereIn('id', $request->log_ids)->count();
        SystemLog::whereIn('id', $request->log_ids)->delete();

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'bulk_delete_logs',
            'details' => ['deleted_count' => $count, 'deleted_at' => now()->toDateTimeString()]
        ]);

        return response()->json([
            'success' => true,
            'message' => __(':count log entries deleted successfully', ['count' => $count])
        ]);
    }

    public function clearByDate(Request $request)
    {
        $request->validate(['date_from' => 'required|date', 'date_to' => 'required|date|after_or_equal:date_from']);
        $dateFrom = Carbon::parse($request->date_from)->startOfDay();
        $dateTo = Carbon::parse($request->date_to)->endOfDay();
        $count = SystemLog::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        SystemLog::whereBetween('created_at', [$dateFrom, $dateTo])->delete();

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'clear_logs_by_date',
            'details' => [
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'deleted_count' => $count,
                'cleared_at' => now()->toDateTimeString()
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __(':count log entries cleared successfully', ['count' => $count])
        ]);
    }

    public function exportCsv(Request $request)
    {
        $query = SystemLog::with('admin');

        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->has('action') && !empty($request->action) && $request->action !== 'all') {
            $query->where('action', $request->action);
        }
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%")
                    ->orWhereHas('admin', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->get();
        $filename = 'system-logs-' . date('Y-m-d-H-i-s') . '.csv';
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['ID', trans('Action'), trans('Admin'), trans('Details'), trans('Date & Time')]);

        foreach ($logs as $log) {
            $actionName = $this->getActionNames()[$log->action] ?? ucfirst(str_replace('_', ' ', $log->action));
            $adminName = $log->admin ? $log->admin->name : 'System';
            $detailsText = $this->formatDetailsText($log->details);
            fputcsv($handle, [
                $log->id,
                $actionName,
                $adminName,
                $detailsText,
                $log->created_at->format('Y-m-d H:i:s')
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // ==================== HELPER METHODS ====================

    private function getActionNames(): array
    {
        return [
            'approve_specialist' => __('Approve Specialist'),
            'reject_specialist' => __('Reject Specialist'),
            'request_info_to_specialist' => __('Request Info from Specialist'),
            'create_content' => __('Create Content'),
            'update_content' => __('Update Content'),
            'delete_content' => __('Delete Content'),
            'publish_content' => __('Publish Content'),
            'unpublish_content' => __('Unpublish Content'),
            'approve_credit_request' => __('Approve Credit Request'),
            'reject_credit_request' => __('Reject Credit Request'),
            'approve_donation' => __('Approve Donation'),
            'reject_donation' => __('Reject Donation'),
            'allocate_donation' => __('Allocate Donation'),
            'process_payout' => __('Process Payout'),
            'process_payout_all' => __('Bulk Payout'),
            'export_users_report' => __('Export Users Report'),
            'export_sessions_report' => __('Export Sessions Report'),
            'export_financial_report' => __('Export Financial Report'),
            'export_specialists_report' => __('Export Specialists Report'),
            'export_points_report' => __('Export Points Report'),
            'export_tests_report' => __('Export Tests Report'),
            'update_specialist' => __('Update Specialist'),
            'delete_specialist' => __('Delete Specialist'),
            'activate_specialist' => __('Activate Specialist'),
            'suspend_specialist' => __('Suspend Specialist'),
            'send_email_to_specialist' => __('Send Email to Specialist'),
            'update_user' => __('Update User'),
            'delete_user' => __('Delete User'),
            'activate_user' => __('Activate User'),
            'suspend_user' => __('Suspend User'),
            'impersonate_user' => __('Impersonate User'),
            'stop_impersonate' => __('Stop Impersonating'),
            'delete_log' => __('Delete Log Entry'),
            'bulk_delete_logs' => __('Bulk Delete Logs'),
            'clear_logs_by_date' => __('Clear Logs by Date'),
        ];
    }

    private function getActionIcons(): array
    {
        return [
            'approve_specialist' => 'fa-check-circle',
            'reject_specialist' => 'fa-times-circle',
            'request_info_to_specialist' => 'fa-question-circle',
            'create_content' => 'fa-plus-circle',
            'update_content' => 'fa-edit',
            'delete_content' => 'fa-trash-alt',
            'publish_content' => 'fa-globe',
            'unpublish_content' => 'fa-eye-slash',
            'approve_credit_request' => 'fa-credit-card',
            'reject_credit_request' => 'fa-credit-card',
            'approve_donation' => 'fa-hand-holding-heart',
            'reject_donation' => 'fa-hand-holding-heart',
            'allocate_donation' => 'fa-hand-holding-heart',
            'process_payout' => 'fa-money-bill-wave',
            'process_payout_all' => 'fa-money-bill-wave',
            'export_users_report' => 'fa-file-excel',
            'export_sessions_report' => 'fa-file-excel',
            'export_financial_report' => 'fa-file-excel',
            'export_specialists_report' => 'fa-file-excel',
            'export_points_report' => 'fa-file-excel',
            'export_tests_report' => 'fa-file-excel',
            'update_specialist' => 'fa-edit',
            'delete_specialist' => 'fa-trash-alt',
            'activate_specialist' => 'fa-play-circle',
            'suspend_specialist' => 'fa-ban',
            'send_email_to_specialist' => 'fa-envelope',
            'update_user' => 'fa-edit',
            'delete_user' => 'fa-trash-alt',
            'activate_user' => 'fa-play-circle',
            'suspend_user' => 'fa-ban',
            'impersonate_user' => 'fa-user-secret',
            'stop_impersonate' => 'fa-sign-out-alt',
            'delete_log' => 'fa-trash-alt',
            'bulk_delete_logs' => 'fa-trash-alt',
            'clear_logs_by_date' => 'fa-calendar-alt',
        ];
    }

    private function getActionColors(): array
    {
        return [
            'approve_specialist' => 'success',
            'reject_specialist' => 'danger',
            'request_info_to_specialist' => 'warning',
            'create_content' => 'success',
            'update_content' => 'info',
            'delete_content' => 'danger',
            'publish_content' => 'success',
            'unpublish_content' => 'warning',
            'approve_credit_request' => 'success',
            'reject_credit_request' => 'danger',
            'approve_donation' => 'success',
            'reject_donation' => 'danger',
            'allocate_donation' => 'success',
            'process_payout' => 'success',
            'process_payout_all' => 'success',
            'export_users_report' => 'info',
            'export_sessions_report' => 'info',
            'export_financial_report' => 'info',
            'export_specialists_report' => 'info',
            'export_points_report' => 'info',
            'export_tests_report' => 'info',
            'update_specialist' => 'info',
            'delete_specialist' => 'danger',
            'activate_specialist' => 'success',
            'suspend_specialist' => 'warning',
            'send_email_to_specialist' => 'info',
            'update_user' => 'info',
            'delete_user' => 'danger',
            'activate_user' => 'success',
            'suspend_user' => 'warning',
            'impersonate_user' => 'warning',
            'stop_impersonate' => 'info',
            'delete_log' => 'danger',
            'bulk_delete_logs' => 'danger',
            'clear_logs_by_date' => 'danger',
        ];
    }

    private function formatDetailsShort($details): string
    {
        if (!is_array($details)) {
            $decoded = json_decode($details, true);
            if (json_last_error() === JSON_ERROR_NONE)
                $details = $decoded;
            else
                return is_string($details) ? substr($details, 0, 100) : '';
        }
        if (empty($details))
            return '-';

        if (isset($details['user_name']))
            return $details['user_name'] . (isset($details['user_email']) ? ' (' . $details['user_email'] . ')' : '');
        if (isset($details['specialist_name']))
            return $details['specialist_name'] . (isset($details['specialist_email']) ? ' (' . $details['specialist_email'] . ')' : '');
        if (isset($details['donor_name']))
            return $details['donor_name'] . (isset($details['donor_email']) ? ' (' . $details['donor_email'] . ')' : '');
        if (isset($details['recipient_name']))
            return $details['recipient_name'];
        if (isset($details['content_title']))
            return $details['content_title'];
        if (isset($details['amount']))
            return '$' . number_format($details['amount'], 2);
        if (isset($details['deleted_count']))
            return $details['deleted_count'] . ' ' . __('items deleted');
        if (isset($details['report_type']))
            return __('Exported') . ': ' . ucfirst(str_replace('_', ' ', $details['report_type']));

        foreach ($details as $value) {
            if (is_string($value) && !empty($value) && strlen($value) < 100)
                return $value;
            if (is_numeric($value) && $value > 0)
                return (string) $value;
        }
        return json_encode($details, JSON_UNESCAPED_UNICODE);
    }

    private function formatDetailsHtml($details): string
    {
        if (!is_array($details)) {
            $decoded = json_decode($details, true);
            if (json_last_error() === JSON_ERROR_NONE)
                $details = $decoded;
            else
                return nl2br(htmlspecialchars($details ?? ''));
        }
        if (empty($details))
            return '<em>' . __('No additional details') . '</em>';

        $html = '<div class="log-details-list">';
        foreach ($details as $key => $value) {
            if ($value === null || $value === '')
                continue;
            $keyName = ucfirst(str_replace('_', ' ', $key));
            $displayValue = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
            if ((str_contains($key, 'amount') || str_contains($key, 'fee') || str_contains($key, 'price')) && is_numeric($value)) {
                $displayValue = '$' . number_format((float) $value, 2);
            }
            $html .= '<div class="detail-row"><span class="detail-key">' . htmlspecialchars($keyName) . ':</span>';
            $html .= '<span class="detail-value">' . nl2br(htmlspecialchars((string) $displayValue)) . '</span></div>';
        }
        $html .= '</div>';
        return $html;
    }

    private function formatDetailsText($details): string
    {
        if (!is_array($details)) {
            $decoded = json_decode($details, true);
            if (json_last_error() === JSON_ERROR_NONE)
                $details = $decoded;
            else
                return is_string($details) ? $details : '';
        }
        if (empty($details))
            return '';

        $text = '';
        foreach ($details as $key => $value) {
            if ($value === null || $value === '')
                continue;
            $keyName = ucfirst(str_replace('_', ' ', $key));
            $displayValue = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
            if ((str_contains($key, 'amount') || str_contains($key, 'fee') || str_contains($key, 'price')) && is_numeric($value)) {
                $displayValue = '$' . number_format((float) $value, 2);
            }
            $text .= $keyName . ': ' . $displayValue . "\n";
        }
        return trim($text);
    }
}