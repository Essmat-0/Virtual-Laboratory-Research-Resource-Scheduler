<div id="audit-trail" class="audit-section">
    <div class="section-head">
        <span class="section-title">Audit Trail</span>
        <span style="font-family:var(--mono); font-size:.62rem; color:var(--text-3)">
            Last 50 actions · Showing {{ $auditLogs->count() }} records
        </span>
    </div>

    <div class="panel">
        <table class="data-table audit-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Action</th>
                    <th>Performed By</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($auditLogs as $log)
                    <tr>
                        <td class="audit-id">{{ str_pad($log->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="audit-action">{{ $log->action }}</td>
                        <td class="audit-user">{{ $log->user_name }}</td>
                        <td class="audit-time">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="4">No audit records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
