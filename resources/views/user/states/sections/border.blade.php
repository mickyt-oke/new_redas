{{-- Border Management directorate sections — shared partial.
     Rendered both by the standalone directorate page (user.directorates.border,
     which re-adds the Preview tab + attachments upload in its wrapper) and by
     the combined state form inside <div class="state-dir-panel" id="dir-border">.
     All tab ids/data-tabs are prefixed with "border-" so they stay unique when
     all ten directorate forms share one page. --}}

<style>
.border-actions { display:flex; justify-content:space-between; align-items:center; gap:10px; padding:14px 0; margin-top:10px; border-top:1px solid var(--gray-200); position:sticky; bottom:0; background:#fff; z-index:20; }
.border-actions-center { display:flex; gap:10px; }
.border-preview-table { width:100%; border-collapse:collapse; font-size:.82rem; margin-bottom:14px; }
.border-preview-table th, .border-preview-table td { border:1px solid var(--gray-200); padding:6px 8px; text-align:left; }
.border-preview-table th { background:#f8fafc; font-weight:700; }
.border-preview-section-title { font-size:.9rem; font-weight:700; margin:18px 0 8px; color:var(--nis-700); border-bottom:1px solid var(--gray-200); padding-bottom:4px; }
</style>

<!-- ==========================================================
    REDAS SECTION NAVIGATION
========================================================== -->

<div class="entry-tabs-wrap">

    <div class="entry-tabs" id="entryTabs">

        @php

        $tabs = [

            ['personnel', 'fas fa-users', 'Personnel'],

            ['land-border', 'fas fa-road', 'Land Border'],

            ['border-nationality', 'fas fa-passport', 'Border Nationality'],

            ['seaport', 'fas fa-anchor', 'Seaport & Marine'],

            ['airports', 'fas fa-plane-departure', 'Int. Airports'],

            ['offshore', 'fas fa-industry', 'Offshore'],

            ['comments', 'fas fa-comments', 'General Reports'],

        ];

        @endphp

        @foreach($tabs as $index => $tab)

            <button
                type="button"
                class="entry-tab {{ $index == 0 ? 'active' : '' }}"
                data-tab="border-{{ $tab[0] }}">

                <i class="{{ $tab[1] }}"></i>

                <span>{{ $tab[2] }}</span>

            </button>

        @endforeach

    </div>

</div>

<!-- ==========================================================
    TAB CONTENTS
========================================================== -->

<div class="tab-content-wrapper">

    <!-- ======================================================
        PERSONNEL (STAFF STRENGTH)
    ======================================================= -->

    <div class="tab-panel active" id="tab-border-personnel">
        <div class="redas-card" style="margin-bottom:14px;">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                            <i class="fas fa-users"></i>
                        </div>
                        1. Personnel
                        <small style="font-weight:400;color:var(--gray-500);">(Current nominal roll to be attached)</small>
                    </div>
                </div>
                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="nis-table staff-table">

                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th style="width:120px;">Male</th>
                                        <th style="width:120px;">Female</th>
                                        <th style="width:120px;">Total</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php
                                        $cadres = [
                                            'Deputy Comptroller General',
                                            'Assistant Comptroller General',
                                            'Comptroller of Immigration',
                                            'Deputy Comptroller of Immigration',
                                            'Assistant Comptroller of Immigration',
                                            'Chief Superintendent of Immigration',
                                            'Superintendent of Immigration',
                                            'Deputy Superintendent of Immigration',
                                            'Assistant Superintendent I',
                                            'Assistant Superintendent II',
                                            'Inspector of Immigration',
                                            'Assistant Inspector of Immigration',
                                            'Immigration Assistant I',
                                            'Immigration Assistant II',
                                            'Immigration Assistant III'
                                        ];
                                    @endphp
                                    @foreach($cadres as $cadre)
                                        @php
                                            $slug = Str::slug($cadre);
                                        @endphp
                                        <tr>

                                            <td>{{ $cadre }}</td>
                                            <td>
                                                <input
                                                    type="number"
                                                    class="ni male staff-input"
                                                    name="border[staff][{{ $slug }}][male]"
                                                    min="0"
                                                    value="0">
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    class="ni female staff-input"
                                                    name="border[staff][{{ $slug }}][female]"
                                                    min="0"
                                                    value="0">
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    class="ni total"
                                                    readonly
                                                    value="0">
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td><strong>TOTAL</strong></td>
                                        <td>
                                            <input
                                                type="number"
                                                id="grandMale"
                                                class="ni"
                                                readonly>
                                        </td>
                                        <td>
                                            <input
                                                type="number"
                                                id="grandFemale"
                                                class="ni"
                                                readonly>
                                        </td>
                                        <td>
                                            <input
                                                type="number"
                                                id="grandTotal"
                                                class="ni"
                                                readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        <div class="border-actions">
            <button type="button" class="btn-nis btn-ghost border-prev-btn" disabled><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="border-actions-center">
                <button type="button" class="btn-nis btn-outline-nis border-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis border-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
            </div>



  <!-- ======================================================
    LAND BORDER
====================================================== -->

<div class="tab-panel" id="tab-border-land-border">

    @php

        $states = [

            'Adamawa' => [
                'Sahuda',
                'Michika',
                'Wuro Alhaji',
                'Kojoli',
                'Belel',
                'Gurin',
                'Wuro-Boki',
                'Tip San'
            ],

            'Akwa Ibom' => [
                'Adadia',
                'Nwaniba',
                'Yoho'
            ],

            'Benue' => [
                'Jato-Aka',
                'Abande'
            ],

            'Borno' => [
                'Gamboru',
                'Kirawa',
                'Damasak',
                'Baga',
                'Sigal',
                'Jilbe',
                'Malamfatori',
                'Banki',
                'Dutse'
            ],

            'Cross River' => [
                'Mfum',
                'Utanga Amana',
                'Ikang',
                'Agbikim',
                'Ekang'
            ],

            'Jigawa' => [
                'Dan Gwanki',
                'Maigatari',
                'Galadi'
            ],

            'Katsina' => [
                'Jibiya',
                'Dan Kama',
                'Kongolom',
                'Zangon Daura',
                'Mai Adua',
                'Babban Mutum'
            ],

            'Kebbi' => [
                'Kamba',
                'Kangiwa',
                'Dole Kaina',
                'Lollo',
                'Bagudo (Maje, Tsamiya)'
            ],

            'Kwara' => [
                'Chikanda',
                'Kosu-Buso',
                'Okuta',
                'Ilesha-Baruba',
                'Kenu',
                'Kaiama',
                'Shiya',
                'Bonya-Ogamue'
            ],

            'Lagos' => [
                'Seme',
                'Owode'
            ],

            'Niger' => [
                'Babana'
            ],

            'Ogun' => [
                'Idiroko',
                'Iyana-Ologo',
                'Ijofin',
                'Ijoun/Tobolo',
                'Ilara/Alagbe',
                'Idopetu',
                'Ohunbe/Obelle',
                'Iwoye',
                'Alaari/Madoga'
            ],

            'Oyo' => [
                'Aiyegun Wasimi',
                'Okerete',
                'Igbokoko'
            ],

            'Sokoto' => [
                'Illela',
                'Gada',
                'Sobon Birni',
                'Tangaza'
            ],

            'Taraba' => [
                'Gembu',
                'Kan Iyaka',
                'Tamnya',
                'Dorofi',
                'Yerimaru',
                'Mayo Dule',
                'Abong',
                'Bissaula',
                'Chana',
                'Ndum-Yaji',
                'Bangdown'
            ],

            'Yobe' => [
                'Machina',
                'Nguru',
                'Tulun Tuluwa',
                'Geidam'
            ],

            'Zamfara' => [
                'Gurbin Baure',
                'Kauran Namoda',
                'Shinkafi'
            ]

        ];

    @endphp

    <div class="redas-card" style="margin-bottom:14px;">

        <div class="card-head">

            <div class="card-head-title">

                <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                    <i class="fas fa-road"></i>
                </div>

                2. Land Border

            </div>

        </div>

        <div class="card-body">

            @foreach($states as $state => $posts)

                @php
                    $stateSlug = Str::slug($state);
                @endphp

                <div class="redas-card mb-3">

                    <div class="card-head">

                        <div class="card-head-title">

                            <div class="card-head-icon"
                                 style="background:#dcfce7;color:#166534;">

                                <i class="fas fa-map-marker-alt"></i>

                            </div>

                            {{ strtoupper($state) }} STATE

                        </div>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="nis-table land-border-table">

                                <thead>

                                    <tr>

                                        <th rowspan="2">S/N</th>
                                        <th rowspan="2">Control Post</th>

                                        <th colspan="3" class="text-center">
                                            ARRIVAL
                                        </th>

                                        <th colspan="3" class="text-center">
                                            DEPARTURE
                                        </th>

                                    </tr>

                                    <tr>

                                        <th>Male</th>
                                        <th>Female</th>
                                        <th>Total</th>

                                        <th>Male</th>
                                        <th>Female</th>
                                        <th>Total</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($posts as $index => $post)

                                        <tr>

                                            <td>{{ $index + 1 }}</td>

                                            <td>{{ $post }}</td>

                                            <td>
                                                <input
                                                    type="number"
                                                    class="ni arrival-male"
                                                    name="border[land][{{$stateSlug}}][{{$index}}][arrival_male]"
                                                    min="0"
                                                    value="0">
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    class="ni arrival-female"
                                                    name="border[land][{{$stateSlug}}][{{$index}}][arrival_female]"
                                                    min="0"
                                                    value="0">
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    class="ni arrival-total"
                                                    readonly>
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    class="ni departure-male"
                                                    name="border[land][{{$stateSlug}}][{{$index}}][departure_male]"
                                                    min="0"
                                                    value="0">
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    class="ni departure-female"
                                                    name="border[land][{{$stateSlug}}][{{$index}}][departure_female]"
                                                    min="0"
                                                    value="0">
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    class="ni departure-total"
                                                    readonly>
                                            </td>

                                        </tr>

                                    @endforeach

                                    <tr class="total-row">

                                        <td colspan="2">
                                            <strong>TOTAL</strong>
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                id="{{$stateSlug}}ArrivalMale"
                                                class="ni"
                                                readonly>
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                id="{{$stateSlug}}ArrivalFemale"
                                                class="ni"
                                                readonly>
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                id="{{$stateSlug}}ArrivalTotal"
                                                class="ni"
                                                readonly>
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                id="{{$stateSlug}}DepartureMale"
                                                class="ni"
                                                readonly>
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                id="{{$stateSlug}}DepartureFemale"
                                                class="ni"
                                                readonly>
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                id="{{$stateSlug}}DepartureTotal"
                                                class="ni"
                                                readonly>
                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @endforeach

        </div> {{-- /.card-body --}}

    </div> {{-- /.redas-card --}}

    <div class="border-actions">
        <button type="button" class="btn-nis btn-ghost border-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
        <div class="border-actions-center">
            <button type="button" class="btn-nis btn-outline-nis border-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
        </div>
        <button type="button" class="btn-nis btn-primary-nis border-next-btn">Next <i class="fas fa-arrow-right"></i></button>
    </div>

</div> {{-- /.tab-panel --}}

    <!-- ======================================================
    BORDER NATIONALITY
    ====================================================== -->

<div class="tab-panel" id="tab-border-border-nationality">

    <!-- ================= HEADER ================= -->

    <div class="redas-card mb-4">

        <div class="card-head">

            <div class="card-head-title">

                <div class="card-head-icon"
                     style="background:#DBEAFE;color:#1D4ED8;">
                    <i class="fas fa-passport"></i>
                </div>
                3. Border Nationality
            </div>

            <div class="card-head-action">
                <button
                    type="button"
                    id="btnAddCommand"
                    class="btn btn-success btn-sm">
                    <i class="fas fa-plus-circle"></i>
                    Add Border Command
                </button>
            </div>
        </div>
    </div>


    <!-- ================= COMMANDS WILL BE INSERTED HERE ================= -->

    <div id="commandContainer"></div>


    <!-- ================= OVERALL SUMMARY ================= -->

    <div class="redas-card mt-4">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon"
                     style="background:#ECFDF5;color:#15803D;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                Overall Summary
            </div>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="nis-table">

                    <thead>

                        <tr>

                            <th>Arrival Male</th>
                            <th>Arrival Female</th>
                            <th>Arrival Total</th>
                            <th>Departure Male</th>
                            <th>Departure Female</th>
                            <th>Departure Total</th>
                            <th>Grand Total</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td><input id="overallArrivalMale" class="ni" readonly></td>

                            <td><input id="overallArrivalFemale" class="ni" readonly></td>

                            <td><input id="overallArrivalTotal" class="ni" readonly></td>

                            <td><input id="overallDepartureMale" class="ni" readonly></td>

                            <td><input id="overallDepartureFemale" class="ni" readonly></td>

                            <td><input id="overallDepartureTotal" class="ni" readonly></td>

                            <td><input id="overallGrandTotal" class="ni" readonly></td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- =========================================================
        BORDER COMMAND TEMPLATE
        __CMD__ is replaced with the command card index by the
        inline script when the template is cloned.
    ========================================================== -->

    <template id="commandTemplate">

        <div class="redas-card nationality-card mb-4">

            <div class="card-head">

                <div class="row w-100 align-items-end">

                    <div class="col-md-5">

                        <label class="form-label fw-bold">

                            State / Command

                        </label>

                        <select class="form-select command-state"
                                name="border[nationality][__CMD__][state]">

                            <option value="">

                                Select State

                            </option>

                        </select>

                    </div>

                    <div class="col-md-5">

                        <label class="form-label fw-bold">

                            Control Post

                        </label>

                        <select class="form-select control-post"
                                name="border[nationality][__CMD__][control_post]">

                            <option value="">

                                Select Control Post

                            </option>

                        </select>

                    </div>

                    <div class="col-md-2 text-end">

                        <button
                            type="button"
                            class="btn btn-danger btn-sm btnRemoveCommand">

                            <i class="fas fa-trash"></i>

                        </button>

                    </div>

                </div>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="nis-table nationality-table">

                        <thead>

                            <tr>

                                <th rowspan="2" width="60">S/N</th>

                                <th rowspan="2">Nationality</th>

                                <th colspan="3">Arrival</th>

                                <th colspan="3">Departure</th>

                                <th rowspan="2">Grand Total</th>

                                <th rowspan="2">Action</th>

                            </tr>

                            <tr>

                                <th>Male</th>
                                <th>Female</th>
                                <th>Total</th>

                                <th>Male</th>
                                <th>Female</th>
                                <th>Total</th>

                            </tr>

                        </thead>

                        <tbody>

                        </tbody>

                        <tfoot>

                            <tr class="table-secondary">

                                <th colspan="2">TOTAL</th>

                                <td><input class="ni totalArrivalMale" readonly></td>

                                <td><input class="ni totalArrivalFemale" readonly></td>

                                <td><input class="ni totalArrival" readonly></td>

                                <td><input class="ni totalDepartureMale" readonly></td>

                                <td><input class="ni totalDepartureFemale" readonly></td>

                                <td><input class="ni totalDeparture" readonly></td>

                                <td><input class="ni totalGrand" readonly></td>

                                <td></td>

                            </tr>

                        </tfoot>

                    </table>

                    <div class="mt-3">

                        <button
                            type="button"
                            class="add-row-btn btnAddNationalityRow">

                            <i class="fas fa-plus"></i>

                            Add Row

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </template>


    <!-- =========================================================
        NATIONALITY ROW TEMPLATE
        __CMD__ is replaced with the owning command card index by
        the inline script when a row is added.
    ========================================================== -->

    <template id="nationalityRowTemplate">

        <tr>

            <td class="sn"></td>

            <td>

                <select class="form-select form-select-sm nationality-select"
                        name="border[nationality][__CMD__][rows][nationality][]">

                    <option value="">

                        Select Nationality

                    </option>

                </select>

            </td>

            <td><input type="number" class="ni arrivalMale" name="border[nationality][__CMD__][rows][arrival_male][]" value="0" min="0"></td>
            <td><input type="number" class="ni arrivalFemale" name="border[nationality][__CMD__][rows][arrival_female][]" value="0" min="0"></td>
            <td><input class="ni arrivalTotal" readonly></td>
            <td><input type="number" class="ni departureMale" name="border[nationality][__CMD__][rows][departure_male][]" value="0" min="0"></td>
            <td><input type="number" class="ni departureFemale" name="border[nationality][__CMD__][rows][departure_female][]" value="0" min="0"></td>
            <td><input class="ni departureTotal" readonly></td>
            <td><input class="ni grandTotal" readonly></td>
            <td class="text-center">
                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger btnRemoveNationality">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    </template>

    <div class="border-actions">
        <button type="button" class="btn-nis btn-ghost border-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
        <div class="border-actions-center">
            <button type="button" class="btn-nis btn-outline-nis border-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
        </div>
        <button type="button" class="btn-nis btn-primary-nis border-next-btn">Next <i class="fas fa-arrow-right"></i></button>
    </div>
</div>
