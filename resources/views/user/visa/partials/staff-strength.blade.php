{{-- ============================================================
    SECTION 1 : STAFF STRENGTH
============================================================= --}}

<div id="staffStrengthCard" class="visa-card">

    <!-- Section Header -->
    <button
        type="button"
        id="staffStrengthToggle"
        class="visa-accordion-header"
    >
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h5>STAFF STRENGTH</h5>
                <small>Annual Staff Distribution by Rank</small>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:15px;">
            <span
                class="section-status status-progress"
                id="staffStatus">
                In Progress
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <!-- Section Body -->
    <div class="visa-card-body">
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">STAFF STRENGTH</strong>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:60px;">S/N</th>
                        <th style="text-align:left">RANK</th>
                        <th>MALE</th>
                        <th>FEMALE</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>

                <tbody id="staff_strength_tbody">
                    @php
                    $ranks = [
                        'dcg'   => 'DCG',
                        'acg'   => 'ACG',
                        'cis'   => 'CIS',
                        'dci'   => 'DCI',
                        'aci'   => 'ACI',
                        'csi'   => 'CSI',
                        'si'    => 'SI',
                        'dsi'   => 'DSI',
                        'asi_1' => 'ASI 1',
                        'asi_2' => 'ASI 2',
                        'ii'    => 'II',
                        'aii'   => 'AII',
                        'ia1'   => 'IA1',
                        'ia2'   => 'IA2',
                        'ia3'   => 'IA3',
                    ];
                    @endphp

                    @foreach($ranks as $key=>$value)
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">{{ $loop->iteration }}</td>
                        <td style="font-weight:600;">
                            <input type="hidden" name="staff[{{ $key }}][cadre]" value="{{ $value }}">
                            {{ $value }}
                        </td>
                        <td>
                            <input
                                type="number"
                                min="0"
                                value="0"
                                id="{{ $key }}_male"
                                name="staff[{{ $key }}][male]"
                                class="ni staff-input male"
                            >
                        </td>
                        <td>
                            <input
                                type="number"
                                min="0"
                                value="0"
                                id="{{ $key }}_female"
                                name="staff[{{ $key }}][female]"
                                class="ni staff-input female"
                            >
                        </td>
                        <td>
                            <input
                                readonly
                                id="{{ $key }}_total"
                                class="ni total-input"
                                value="0"
                            >
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="visa-total-row">
                        <th colspan="2">TOTAL</th>
                        <th>
                            <input readonly id="grandMale" class="ni total-input" value="0">
                        </th>
                        <th>
                            <input readonly id="grandFemale" class="ni total-input" value="0">
                        </th>
                        <th>
                            <input readonly id="grandTotal" class="ni total-input" value="0">
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Staff Strength :
                <strong id="staffSummary">0 Officers</strong>
            </div>

            <div class="section-buttons">
                <button
                    type="button"
                    class="btn-nis btn-ghost"
                    id="resetStaffStrength">
                    <i class="fas fa-rotate-left"></i>
                    Reset
                </button>

                <button
                    type="button"
                    class="btn-nis btn-primary-nis"
                    id="saveStaffStrength">
                    <i class="fas fa-check-circle"></i>
                    Save Section
                </button>
            </div>
        </div>
    </div>
</div>