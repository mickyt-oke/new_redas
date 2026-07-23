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

        <!-- Upload Card -->
        <div class="redas-card animate-fade-up" style="margin-bottom:24px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-cloud-upload-alt"></i></div>
                    Upload Archive Document
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ url('/user/archive/upload') }}" enctype="multipart/form-data" id="archiveForm">
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
                        <textarea name="description" class="ni" rows="2" placeholder="Brief description of the document(s)..."></textarea>
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

        <!-- Archive List -->
        <div class="redas-card animate-fade-up delay-1">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-archive"></i></div>
                    Archived Documents
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="search" id="archiveSearch" class="ni" placeholder="Search documents..." style="width:200px;font-size:.82rem;padding:6px 10px;">
                    <select id="archiveFilter" class="ni ni-select" style="font-size:.82rem;padding:6px 10px;width:auto;">
                        <option value="">All Types</option>
                        <option value="Monthly Return">Monthly Return</option>
                        <option value="Quarterly Return">Quarterly Return</option>
                        <option value="Annual Return">Annual Return</option>
                        <option value="Special Report">Special Report</option>
                        <option value="Nominal Roll">Nominal Roll</option>
                        <option value="Supporting Document">Supporting Document</option>
                    </select>
                </div>
            </div>
            <div class="card-body no-pad">
                <table class="redas-table searchable-table" id="archiveTable">
                    <thead>
                        <tr>
                            <th>Document Title</th>
                            <th>Type</th>
                            <th>Period</th>
                            <th>Uploaded</th>
                            <th>Size</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([
                            ['January 2025 Monthly Return',     'Monthly Return',     'Jan 2025', '01 Feb 2025', '2.4 MB',  'archived'],
                            ['Q1 2025 Quarterly Return',        'Quarterly Return',   'Q1 2025',  '05 Apr 2025', '3.1 MB',  'archived'],
                            ['December 2024 Monthly Return',    'Monthly Return',     'Dec 2024', '02 Jan 2025', '1.9 MB',  'archived'],
                            ['2024 Annual Report',              'Annual Return',      '2024',     '15 Jan 2025', '5.7 MB',  'archived'],
                            ['Nominal Roll — April 2025',       'Nominal Roll',       'Apr 2025', '01 May 2025', '890 KB',  'archived'],
                            ['Border Movement Data — Q4 2024',  'Supporting Document','Q4 2024',  '10 Jan 2025', '1.2 MB',  'archived'],
                        ] as [$title, $type, $period, $date, $size, $status])
                        <tr class="archive-row" data-type="{{ $type }}">
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:32px;height:32px;border-radius:var(--radius-sm);background:var(--nis-50);color:var(--nis-600);display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <span style="font-weight:600;font-size:.86rem;">{{ $title }}</span>
                                </div>
                            </td>
                            <td style="font-size:.8rem;">{{ $type }}</td>
                            <td style="font-size:.8rem;color:var(--gray-600);">{{ $period }}</td>
                            <td style="font-size:.78rem;color:var(--gray-400);">{{ $date }}</td>
                            <td style="font-size:.78rem;color:var(--gray-400);">{{ $size }}</td>
                            <td>
                                <span class="status-badge badge-approved">Archived</span>
                            </td>
                            <td>
                                <div style="display:flex;gap:4px;">
                                    <button class="btn-nis btn-ghost btn-sm" title="View" onclick="REDAS.showToast('Opening document…','info')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-nis btn-ghost btn-sm" title="Download" onclick="REDAS.showToast('Downloading…','success')">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
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

// Archive search / filter
const searchInput = document.getElementById('archiveSearch');
const filterSelect = document.getElementById('archiveFilter');
function filterArchive() {
    const q = searchInput.value.toLowerCase();
    const t = filterSelect.value.toLowerCase();
    document.querySelectorAll('#archiveTable tbody .archive-row').forEach(row => {
        const title = row.querySelector('td:first-child').textContent.toLowerCase();
        const type  = (row.dataset.type || '').toLowerCase();
        row.style.display = (title.includes(q) && (!t || type.includes(t))) ? '' : 'none';
    });
}
searchInput.addEventListener('input', filterArchive);
filterSelect.addEventListener('change', filterArchive);

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
