{{--
    Shared in-browser document / image preview modal.
    Required variables:
        - $docRoute: route name (or closure) used to generate each document URL.
          The partial will call route($docRoute, [$application, $collection, $index]).
        - $application: the submission model whose return_data contains the file paths.
    Optional:
        - $groupKeys: map of return_data key => [collection, label]. Defaults are provided.

    Each invoking view is responsible for adding the .doc-card click handlers by
    including this partial after it has rendered its document cards.
--}}
@php
    $groupKeys = $groupKeys ?? [
        'supporting_documents' => ['supporting', 'Supporting Documents'],
        'attachments' => ['attachments', 'Attachments'],
    ];

    $documentGroups = [];
    foreach ($groupKeys as $key => [$collection, $label]) {
        $paths = array_values(array_filter((array) (($application->return_data ?? [])[$key] ?? []), 'is_string'));
        if ($paths !== []) {
            $documentGroups[] = [$collection, $label, $paths];
        }
    }
@endphp

<style>
    .doc-card { display:flex; align-items:center; gap:10px; padding:12px 14px; border:1px solid var(--gray-200, #e5e7eb); border-radius:10px; cursor:pointer; background:#fff; transition:box-shadow .15s; }
    .doc-card:hover { box-shadow:0 2px 8px rgba(0,0,0,.12); border-color:var(--nis-300, #86efac); }
    .doc-card .doc-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
    .doc-card .doc-name { font-size:.82rem; font-weight:600; color:var(--gray-800, #1f2937); word-break:break-all; }
    .doc-card .doc-hint { font-size:.72rem; color:var(--gray-500, #6b7280); }
    #docViewerModal { display:none; position:fixed; inset:0; z-index:1000; background:rgba(15,23,42,.65); align-items:center; justify-content:center; padding:24px; }
    #docViewerModal.open { display:flex; }
    #docViewerBox { background:#fff; border-radius:14px; width:100%; max-width:960px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; }
    #docViewerBody { flex:1; overflow:auto; display:flex; align-items:center; justify-content:center; background:#f8fafc; min-height:300px; }
    #docViewerBody img { max-width:100%; max-height:75vh; object-fit:contain; }
    #docViewerBody iframe { width:100%; height:75vh; border:none; }
</style>

@if($documentGroups !== [])
    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#fdf4e3;color:#a3731a;"><i class="fas fa-paperclip"></i></div>
                Uploaded Documents &amp; Images
            </div>
        </div>
        <div class="card-body">
            @foreach($documentGroups as [$collection, $label, $paths])
                <div class="preview-sub-section-title">{{ $label }}</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px;margin-bottom:12px;">
                    @foreach($paths as $i => $path)
                        @php
                            $name = basename((string) $path);
                            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true);
                            [$iconBg, $iconColor, $icon] = match (true) {
                                $isImage => ['#ecfdf5', '#15803d', 'fa-image'],
                                $ext === 'pdf' => ['#fef2f2', '#b91c1c', 'fa-file-pdf'],
                                in_array($ext, ['xls', 'xlsx', 'csv'], true) => ['#eff6ff', '#1d4ed8', 'fa-file-excel'],
                                in_array($ext, ['doc', 'docx'], true) => ['#eff6ff', '#1d4ed8', 'fa-file-word'],
                                default => ['#f8fafc', '#475569', 'fa-file'],
                            };
                            $docUrl = \Illuminate\Support\Facades\URL::signedRoute($docRoute, ['applicationHash' => \App\Services\HashidService::encode($application->id), 'collection' => $collection, 'index' => $i]);
                            $downloadUrl = \Illuminate\Support\Facades\URL::signedRoute($docRoute, ['applicationHash' => \App\Services\HashidService::encode($application->id), 'collection' => $collection, 'index' => $i, 'download' => 1]);
                        @endphp
                        <div class="doc-card"
                             data-doc-url="{{ $docUrl }}"
                             data-download-url="{{ $downloadUrl }}"
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
        </div>
    </div>
@endif

{{-- Modal --}}
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
    if (!modal) return;
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
            downloadLink.href = card.dataset.downloadUrl;
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
