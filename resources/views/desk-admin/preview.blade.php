@extends('desk-admin.layout')

@section('content')
@php
    $data = is_array($application->return_data) ? $application->return_data : [];

    $humanize = function ($key) {
        return ucwords(str_replace(['_', '-'], ' ', (string) $key));
    };

    $isAssoc = function (array $arr) {
        return $arr !== [] && array_keys($arr) !== range(0, count($arr) - 1);
    };

    $formatValue = function ($value) {
        if ($value === null || $value === '') {
            return '—';
        }
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        return (string) $value;
    };

    // Document keys get their own section with a viewer, not the generic tables.
    $skipKeys = ['directorate_slug', 'data_consent', 'report_period', 'reporting_officer', 'supporting_documents', 'attachments'];

    // Same sectional rendering style as the directorate submission preview:
    // assoc arrays -> key/value tables (nested arrays become sub-sections);
    // indexed arrays of arrays -> tables; indexed scalars -> comma lists.
    $renderData = function ($data, int $depth = 0) use (&$renderData, $humanize, $isAssoc, $formatValue, $skipKeys) {
        if (! is_array($data)) {
            return '<span>' . e($formatValue($data)) . '</span>';
        }

        if ($data === []) {
            return '<span>—</span>';
        }

        $out = '';

        if ($isAssoc($data)) {
            $scalars = [];
            $nested = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $skipKeys, true)) {
                    continue;
                }
                if (is_array($value)) {
                    $nested[$key] = $value;
                } else {
                    $scalars[$key] = $value;
                }
            }

            if ($scalars !== []) {
                $out .= '<table class="preview-report-table"><tbody>';
                foreach ($scalars as $key => $value) {
                    $out .= '<tr><td class="kv-key">' . e($humanize($key)) . '</td><td>'
                        . e($formatValue($value)) . '</td></tr>';
                }
                $out .= '</tbody></table>';
            }

            foreach ($nested as $key => $value) {
                $out .= '<div class="' . ($depth === 0 ? 'preview-section-title' : 'preview-sub-section-title') . '">'
                    . e($humanize($key)) . '</div>';
                $out .= $renderData($value, $depth + 1);
            }

            return $out === '' ? '<span>—</span>' : $out;
        }

        $allArrays = count(array_filter($data, 'is_array')) === count($data);

        if (! $allArrays) {
            return '<span>' . e(implode(', ', array_map($formatValue, $data))) . '</span>';
        }

        $columns = [];
        foreach ($data as $row) {
            foreach (array_keys($row) as $col) {
                if (! in_array($col, $columns, true)) {
                    $columns[] = $col;
                }
            }
        }

        $out .= '<table class="preview-report-table"><thead><tr>';
        foreach ($columns as $col) {
            $out .= '<th>' . e($humanize($col)) . '</th>';
        }
        $out .= '</tr></thead><tbody>';
        foreach ($data as $row) {
            $out .= '<tr>';
            foreach ($columns as $col) {
                $cell = $row[$col] ?? null;
                $out .= '<td>' . (is_array($cell) ? $renderData($cell, $depth + 1) : e($formatValue($cell))) . '</td>';
            }
            $out .= '</tr>';
        }
        $out .= '</tbody></table>';

        return $out;
    };

    // Uploaded files grouped for the document viewer.
    $documentGroups = [];
    foreach (['supporting_documents' => ['supporting', 'Supporting Documents'], 'attachments' => ['attachments', 'Attachments']] as $key => [$collection, $label]) {
        $paths = array_values(array_filter((array) ($data[$key] ?? []), 'is_string'));
        if ($paths !== []) {
            $documentGroups[] = [$collection, $label, $paths];
        }
    }

    $status = strtolower((string) $application->status);
    $statusStyle = match (true) {
        $status === 'approved' => 'background:#ecfdf5;color:#166534;border:1px solid #86efac;',
        in_array($status, ['rejected', 'queried', 'returned'], true) => 'background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;',
        default => 'background:#fef9c3;color:#854d0e;border:1px solid #fde047;',
    };
@endphp

<style>
    .preview-section-title { font-size:.95rem; font-weight:700; color:var(--nis-700); margin:20px 0 8px; border-bottom:1px solid var(--gray-200); padding-bottom:4px; }
    .preview-sub-section-title { font-size:.85rem; font-weight:700; color:var(--gray-700); margin:12px 0 6px; }
    table.preview-report-table { width:100%; border-collapse:collapse; font-size:.82rem; margin-bottom:8px; }
    table.preview-report-table th, table.preview-report-table td { border:1px solid var(--gray-200); padding:6px 10px; text-align:left; vertical-align:top; }
    table.preview-report-table th { background:var(--nis-50); font-weight:700; color:var(--nis-800); }
    table.preview-report-table td.kv-key { width:38%; font-weight:600; background:#f9fafb; }
    .doc-card { display:flex; align-items:center; gap:10px; padding:12px 14px; border:1px solid var(--gray-200); border-radius:10px; cursor:pointer; background:#fff; transition:box-shadow .15s; }
    .doc-card:hover { box-shadow:var(--shadow-md); border-color:var(--nis-300); }
    .doc-card .doc-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
    .doc-card .doc-name { font-size:.82rem; font-weight:600; color:var(--gray-800); word-break:break-all; }
    .doc-card .doc-hint { font-size:.72rem; color:var(--gray-500); }
    #docViewerModal { display:none; position:fixed; inset:0; z-index:1000; background:rgba(15,23,42,.65); align-items:center; justify-content:center; padding:24px; }
    #docViewerModal.open { display:flex; }
    #docViewerBox { background:#fff; border-radius:14px; width:100%; max-width:960px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; }
    #docViewerBody { flex:1; overflow:auto; display:flex; align-items:center; justify-content:center; background:#f8fafc; min-height:300px; }
    #docViewerBody img { max-width:100%; max-height:75vh; object-fit:contain; }
    #docViewerBody iframe { width:100%; height:75vh; border:none; }
</style>

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Submission #{{ $application->id }}</h1>
            <p class="page-subtitle">Review the return below before approval or return.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('user.submissions.pdf', $application) }}" class="btn-nis btn-outline-nis">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <a href="{{ route('desk.admin.submissions.download', $application) }}" class="btn-nis btn-outline-nis">
                <i class="fas fa-download"></i> Download CSV
            </a>
            <a href="{{ route('user.desk.home') }}" class="btn-nis btn-ghost">
                <i class="fas fa-arrow-left"></i> Back to Review Dashboard
            </a>
        </div>
    </div>

    @if(session('status'))
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #fca5a5;background:#fef2f2;color:#991b1b;">
            @foreach($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Report header, same style as the directorate return preview --}}
    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-body" style="border-bottom:3px solid var(--nis-700);">
            <h2 style="font-size:1.15rem;margin:0 0 4px;color:var(--nis-700);">Nigeria Immigration Service</h2>
            <p style="margin:2px 0;font-size:.85rem;color:var(--gray-600);">
                {{ $humanize($data['directorate_slug'] ?? $application->scope_code ?? 'State') }} — Monthly Return
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:8px 28px;margin-top:10px;font-size:.85rem;">
                <span><strong>Report Period:</strong> {{ $data['report_period'] ?? '—' }}</span>
                <span><strong>Reporting Officer:</strong> {{ $data['reporting_officer'] ?? optional($application->user)->name ?? '—' }}</span>
                <span><strong>Submitted:</strong> {{ optional($application->created_at)->format('d M Y, H:i') ?? '—' }}</span>
                <span><strong>Status:</strong>
                    <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;{{ $statusStyle }}">
                        {{ ucfirst($application->status ?? 'pending') }}
                    </span>
                </span>
            </div>
        </div>
    </div>

    {{-- Sectional return data --}}
    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#eef2ff;color:#2563eb;"><i class="fas fa-file-lines"></i></div>
                Return Content
            </div>
        </div>
        <div class="card-body">
            @if($data !== [])
                {!! $renderData($data) !!}
            @else
                <div style="padding:12px;color:#64748b;">No return data is available to preview.</div>
            @endif
        </div>
    </div>

    {{-- Supporting documents with in-browser viewer --}}
    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#fdf4e3;color:#a3731a;"><i class="fas fa-paperclip"></i></div>
                Uploaded Documents &amp; Images
            </div>
        </div>
        <div class="card-body">
            @if($documentGroups === [])
                <p style="font-size:.84rem;color:var(--gray-500);margin:0;">No documents or images were uploaded with this return.</p>
            @else
                @foreach($documentGroups as [$collection, $label, $paths])
                    <div class="preview-sub-section-title">{{ $label }}</div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px;margin-bottom:12px;">
                        @foreach($paths as $i => $path)
                            @php
                                $name = basename($path);
                                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true);
                                [$iconBg, $iconColor, $icon] = match (true) {
                                    $isImage => ['#ecfdf5', '#15803d', 'fa-image'],
                                    $ext === 'pdf' => ['#fef2f2', '#b91c1c', 'fa-file-pdf'],
                                    in_array($ext, ['xls', 'xlsx', 'csv'], true) => ['#eff6ff', '#1d4ed8', 'fa-file-excel'],
                                    in_array($ext, ['doc', 'docx'], true) => ['#eff6ff', '#1d4ed8', 'fa-file-word'],
                                    default => ['#f8fafc', '#475569', 'fa-file'],
                                };
                                $docUrl = route('desk.admin.submissions.document', [$application, $collection, $i]);
                            @endphp
                            <div class="doc-card"
                                 data-doc-url="{{ $docUrl }}"
                                 data-doc-name="{{ $name }}"
                                 data-doc-ext="{{ $ext }}">
                                <div class="doc-icon" style="background:{{ $iconBg }};color:{{ $iconColor }};"><i class="fas {{ $icon }}"></i></div>
                                <div>
                                    <div class="doc-name">{{ $name }}</div>
                                    <div class="doc-hint">Click to preview</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Review actions: only while the submission awaits this approver's stage --}}
    <div class="redas-card" style="padding:16px;">
        @if($canReview ?? false)
            <div style="display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;align-items:center;">
                <form method="POST" action="{{ route($approveRoute, $application) }}" style="display:flex;gap:8px;flex-wrap:wrap;margin:0;">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="comment" maxlength="1000" placeholder="Approval note (optional)" style="padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.82rem;min-width:200px;">
                    <button type="submit" class="btn-nis btn-primary-nis"><i class="fas fa-check"></i> Approve</button>
                </form>
                <form method="POST" action="{{ route($rejectRoute, $application) }}" style="display:flex;gap:8px;flex-wrap:wrap;margin:0;">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="comment" maxlength="1000" required placeholder="Rejection reason (required)" style="padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.82rem;min-width:200px;">
                    <button type="submit" class="btn-nis" style="background:#b91c1c;color:#fff;"><i class="fas fa-undo"></i> Return for Correction</button>
                </form>
            </div>
        @else
            <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;">
                <span style="font-size:.84rem;color:var(--gray-500);">This submission is not awaiting your action.</span>
                <a href="{{ route('user.desk.home') }}" class="btn-nis btn-ghost">Back to dashboard</a>
            </div>
        @endif
    </div>
</main>

{{-- Document / image preview modal --}}
<div id="docViewerModal" role="dialog" aria-modal="true">
    <div id="docViewerBox">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 16px;border-bottom:1px solid var(--gray-200);">
            <div id="docViewerTitle" style="font-size:.88rem;font-weight:700;color:var(--gray-800);word-break:break-all;"></div>
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <a id="docViewerDownload" href="#" class="btn-nis btn-sm btn-outline-nis"><i class="fas fa-download"></i> Download</a>
                <button type="button" id="docViewerClose" class="btn-nis btn-sm btn-ghost"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
        <div id="docViewerBody"></div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('docViewerModal');
    var body = document.getElementById('docViewerBody');
    var title = document.getElementById('docViewerTitle');
    var downloadLink = document.getElementById('docViewerDownload');
    var imageExts = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

    function closeViewer() {
        modal.classList.remove('open');
        body.innerHTML = '';
    }

    document.querySelectorAll('.doc-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var url = card.dataset.docUrl;
            var name = card.dataset.docName;
            var ext = card.dataset.docExt;

            title.textContent = name;
            downloadLink.href = url + '?download=1';
            body.innerHTML = '';

            if (imageExts.indexOf(ext) !== -1) {
                var img = document.createElement('img');
                img.src = url;
                img.alt = name;
                body.appendChild(img);
            } else if (ext === 'pdf') {
                var frame = document.createElement('iframe');
                frame.src = url;
                body.appendChild(frame);
            } else {
                body.innerHTML = '<div style="padding:40px;text-align:center;color:var(--gray-600);font-size:.88rem;">' +
                    '<i class="fas fa-file" style="font-size:2rem;color:var(--gray-400);display:block;margin-bottom:10px;"></i>' +
                    'This file type cannot be previewed in the browser.<br>Use the Download button to open it.' +
                    '</div>';
            }

            modal.classList.add('open');
        });
    });

    document.getElementById('docViewerClose').addEventListener('click', closeViewer);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeViewer();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeViewer();
    });
})();
</script>
@endsection
