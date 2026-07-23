{{-- ============================================================
    14. E-DOCUMENTATION/ID CARD ACTIVITIES
============================================================= --}}

<div id="idCardsCard" class="visa-card animate-fade-up">

    <!-- Section Header -->
    <button type="button" id="idCardsToggle" class="visa-accordion-header">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div style="text-align: left;">
                <h5>E-Documentation / ID Card Activities</h5>
                <small>Official ID card distribution registers across SHQ, Zones and special command units</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span class="section-status status-progress" id="idCardsStatus">In Progress</span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <!-- Section Body -->
    <div class="visa-card-body">
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong>ID Card Log Sheet</strong>
            </div>
            <div class="table-toolbar-right">
                <small style="color:#64748b;">Enter card issuances by officer rank and zone</small>
            </div>
        </div>

        @php
        $ranks = [
            'dcg' => 'DCG',
            'acg' => 'ACG',
            'cis' => 'CIS',
            'dci' => 'DCI',
            'aci' => 'ACI',
            'csi' => 'CSI',
            'si' => 'SI',
            'dsi' => 'DSI',
            'asi1' => 'ASI¹',
            'asi2' => 'ASI²',
            'ii' => 'II',
            'aii' => 'AII',
            'cia' => 'CIA',
            'ia1' => 'IA¹',
            'ia2' => 'IA²',
            'ia3' => 'IA³'
        ];

        $zones = [
            'shq' => 'SHQ',
            'zone_a' => "Zone 'A'",
            'zone_b' => "Zone 'B'",
            'zone_c' => "Zone 'C'",
            'zone_d' => "Zone 'D'",
            'zone_e' => "Zone 'E'",
            'zone_f' => "Zone 'F'",
            'zone_g' => "Zone 'G'",
            'zone_h' => "Zone 'H'",
            'icsc' => 'ICSC',
            'itsk' => 'ITSK',
            'nitsol' => 'NITSOL',
            'nitsa' => 'NITSA'
        ];
        @endphp

        <div class="table-responsive">
            <table class="visa-table border" style="font-size:0.75rem;">
                <thead>
                    <tr>
                        <th style="text-align:left;min-width:110px;">Rank</th>
                        @foreach($zones as $zKey => $zVal)
                            <th style="text-align:center;padding:6px;min-width:60px;">{{ $zVal }}</th>
                        @endforeach
                        <th style="text-align:center;min-width:70px;background:#f1f5f9;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranks as $rKey => $rVal)
                    <tr>
                        <td style="font-weight:700;text-align:left;">{{ $rVal }}</td>
                        @foreach($zones as $zKey => $zVal)
                        <td style="padding:4px;text-align:center;">
                            <input type="number" 
                                   class="ni id-card-input text-center" 
                                   data-row="{{ $rKey }}" 
                                   data-col="{{ $zKey }}" 
                                   name="id_cards[{{ $rKey }}][{{ $zKey }}]" 
                                   value="0" 
                                   min="0" 
                                   style="padding:4px;height:30px;min-width:55px;">
                        </td>
                        @endforeach
                        <td style="padding:4px;text-align:center;background:#f8fafc;">
                            <input type="number" 
                                   id="row_total_{{ $rKey }}" 
                                   class="ni total-input id-card-row-total text-center" 
                                   data-row="{{ $rKey }}" 
                                   readonly 
                                   style="padding:4px;height:30px;min-width:65px;font-weight:700;background:var(--gray-50);">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="visa-total-row">
                        <th style="text-align:left;">TOTAL</th>
                        @foreach($zones as $zKey => $zVal)
                        <th style="padding:4px;text-align:center;">
                            <input type="number" 
                                   id="col_total_{{ $zKey }}" 
                                   class="ni total-input id-card-col-total text-center" 
                                   data-col="{{ $zKey }}" 
                                   readonly 
                                   style="padding:4px;height:30px;min-width:55px;font-weight:700;">
                        </th>
                        @endforeach
                        <th style="padding:4px;text-align:center;background:var(--nis-50);">
                            <input type="number" 
                                   id="id_card_grand_total" 
                                   readonly 
                                   class="ni total-input text-center" 
                                   style="padding:4px;height:30px;min-width:65px;font-weight:800;color:var(--nis-800);background:var(--nis-50);">
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Cards Printed/Processed : <strong id="idCardSummary">0 Cards</strong>
            </div>
            <div class="section-buttons">
                <button type="button" class="btn-nis btn-ghost" id="resetIdCards">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="button" class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>

</div>
