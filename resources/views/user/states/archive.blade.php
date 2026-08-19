@include('partials.header')

    <main class="redas-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Archive</h1>
                <p class="page-subtitle">Upload and manage archived documents for your command.</p>
            </div>
            <button class="btn-nis btn-primary-nis" onclick="document.getElementById('archiveUploadInput').click()">
                <i class="fas fa-upload"></i> Upload Document
            </button>
        </div>

        @if(session('status'))
        <div style="background:#dcfce7;border:1px solid #86efac;border-radius:var(--radius-md);padding:12px 18px;display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <i class="fas fa-check-circle" style="color:#15803d;"></i>
            <span style="font-size:.88rem;color:#166534;font-weight:600;">{{ session('status') }}</span>
        </div>
        @endif

        <!-- Data Protection Notice -->
        <div class="redas-card" style="margin-bottom:20px;border-left:4px solid #1d4ed8;">
            <div class="card-body" style="font-size:.82rem;color:var(--gray-600);">
                <div style="display:flex;align-items:flex-start;gap:12px;">
                    <i class="fas fa-shield-alt" style="color:#1d4ed8;font-size:1.1rem;margin-top:2px;"></i>
                    <div>
                        <strong style="color:#1e3a8a;display:block;margin-bottom:4px;">Data Protection Notice</strong>
                        Archived documents may contain personal or sensitive operational data. Upload only documents required for official NIS business.
                        Stored documents are retained in line with NIS archival policy and applicable data-protection law.
                        <a href="{{ route('privacy') }}" target="_blank" style="color:#1d4ed8;text-decoration:underline;">Read the Privacy Policy</a>.
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Card -->
        <div class="redas-card animate-fade-up" style="margin-bottom:24px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-cloud-upload-alt"></i></div>
                    Upload Archive Document
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('user.archive.store') }}" enctype="multipart/form-data" id="archiveForm">
                    @csrf
                    <div class="form-grid-3" style="margin-bottom:16px;">
                        <div class="fg">
                            <label>Document Title</label>
                            <input type="text" name="title" class="ni" placeholder="e.g. January 2025 Monthly Return" required>
                        </div>
                        <div class="fg">
                            <label>Document Type</label>
                            <select name="doc_type" class="ni ni-select" required>
                                <option value="">Select Type</option>
                                <option value="monthly_return">Monthly Return</option>
                                <option value="quarterly_return">Quarterly Return</option>
                                <option value="annual_return">Annual Return</option>
                                <option value="special_report">Special Report</option>
                                <option value="nominal_roll">Nominal Roll</option>
                                <option value="supporting_doc">Supporting Document</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="fg">
                            <label>Reference Period</label>
                            <input type="month" name="period" class="ni" value="{{ now()->format('Y-m') }}">
                        </div>
                    </div>

                    <div class="attach-zone" id="archiveDropZone" style="margin-bottom:16px;cursor:pointer;" onclick="document.getElementById('archiveUploadInput').click()">
                        <i class="fas fa-folder-open" style="font-size:2rem;color:var(--nis-400);margin-bottom:8px;"></i>
                        <div style="font-size:.88rem;color:var(--gray-500);margin-bottom:4px;">Drag &amp; drop files here or click to browse</div>
                        <div style="font-size:.76rem;color:var(--gray-400);">PDF, Word, Excel, Images — max 20MB each</div>
                        <input type="file" name="documents[]" id="archiveUploadInput" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                            style="display:none;"
                            onchange="showSelectedFiles(this)">
                    </div>
                    <div id="selectedFilesList" style="margin-bottom:16px;"></div>

                    <div class="fg" style="margin-bottom:16px;">
                        <label>Description / Remarks</label>
                        <textarea name="description" class="ni" rows="2" placeholder="Brief description of the document(s)..." autocomplete="off" autocorrect="off"></textarea>
                    </div>

                    <div class="fg" style="margin-bottom:16px;">
                        <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:.84rem;color:var(--gray-700);">
                            <input type="checkbox" name="data_consent" value="1" required style="accent-color:var(--nis-600);margin-top:2px;">
                            <span>
                                I confirm that this upload is necessary for official NIS business, that the document does not contain unnecessary personal data,
                                and that it will be handled in accordance with the <a href="{{ route('privacy') }}" target="_blank" style="color:#1d4ed8;text-decoration:underline;">Privacy Policy</a>.
                            </span>
                        </label>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="reset" class="btn-nis btn-ghost btn-sm" onclick="document.getElementById('selectedFilesList').innerHTML=''">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn-nis btn-primary-nis btn-sm">
                            <i class="fas fa-upload"></i> Upload to Archive
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Completed Returns (downloadable PDF) -->
        <div class="redas-card animate-fade-up delay-1" style="margin-bottom:24px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-file-pdf"></i></div>
                    Completed Returns — PDF Archive
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="search" id="archiveSearch" class="ni" placeholder="Search returns..." style="width:200px;font-size:.82rem;padding:6px 10px;">
                </div>
            </div>
            <div class="card-body no-pad">
                <table class="redas-table searchable-table" id="archiveTable">
                    <thead>
                        <tr>
                            <th>Return</th>
                            <th>Officer</th>
                            <th>Period</th>
                            <th>Completed</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($completedReturns ?? collect() as $return)
                            @php
                                $period = $return->return_data['report_period'] ?? null;
                                $title = ($period ? \Carbon\Carbon::createFromFormat('Y-m', $period)->format('F Y') : 'Return')
                                    . ' — Monthly Return';
                            @endphp
                            <tr class="archive-row">
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:32px;height:32px;border-radius:var(--radius-sm);background:#fef2f2;color:#b91c1c;display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div>
                                            <span style="font-weight:600;font-size:.86rem;">{{ $title }}</span>
                                            <div style="font-size:.72rem;color:var(--gray-400);">Submission #{{ $return->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:.8rem;">{{ optional($return->user)->name ?? 'N/A' }}</td>
                                <td style="font-size:.8rem;color:var(--gray-600);">{{ $period ?? '—' }}</td>
                                <td style="font-size:.78rem;color:var(--gray-400);">{{ optional($return->updated_at)->format('d M Y') }}</td>
                                <td>
                                    <span class="status-badge badge-approved">Approved</span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:4px;">
                                        <a href="{{ route('user.submissions.pdf', $return) }}" class="btn-nis btn-ghost btn-sm" title="View PDF" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('user.submissions.pdf', $return) }}" class="btn-nis btn-ghost btn-sm" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:24px;text-align:center;color:var(--gray-500);font-size:.86rem;">
                                    <i class="fas fa-inbox" style="display:block;font-size:1.4rem;color:var(--gray-400);margin-bottom:8px;"></i>
                                    No completed returns are archived yet. Approved returns will appear here as downloadable PDFs.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

<script>
function showSelectedFiles(input) {
    const list = document.getElementById('selectedFilesList');
    list.innerHTML = '';
    if (!input.files.length) return;
    Array.from(input.files).forEach(file => {
        const item = document.createElement('div');
        item.style.cssText = 'display:flex;align-items:center;gap:10px;padding:8px 12px;background:var(--nis-50);border-radius:var(--radius-sm);margin-bottom:6px;font-size:.82rem;';
        item.innerHTML = `<i class="fas fa-file" style="color:var(--nis-600);"></i><span style="flex:1;color:var(--gray-700);">${file.name}</span><span style="color:var(--gray-400);">${(file.size/1024/1024).toFixed(2)} MB</span>`;
        list.appendChild(item);
    });
}

// Archive search
const searchInput = document.getElementById('archiveSearch');
function filterArchive() {
    const q = searchInput.value.toLowerCase();
    document.querySelectorAll('#archiveTable tbody .archive-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
searchInput.addEventListener('input', filterArchive);

// Drag-and-drop highlight
const dropZone = document.getElementById('archiveDropZone');
['dragover','dragenter'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.style.borderColor='var(--nis-500)'; dropZone.style.background='var(--nis-50)'; }));
['dragleave','drop'].forEach(e => dropZone.addEventListener(e, ev => { dropZone.style.borderColor=''; dropZone.style.background=''; }));
dropZone.addEventListener('drop', ev => {
    ev.preventDefault();
    const input = document.getElementById('archiveUploadInput');
    input.files = ev.dataTransfer.files;
    showSelectedFiles(input);
});
</script>

@include('partials.footer')
