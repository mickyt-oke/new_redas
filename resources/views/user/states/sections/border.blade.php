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
                    @if($stateEmbedded ?? false)
                        <p style="font-size:.82rem;color:var(--gray-500);padding:8px 0;">
                            <i class="fas fa-circle-info" style="color:var(--nis-500);margin-right:6px;"></i>
                            Staff strength for this command is captured once under the <strong>HRM</strong> section of this return.
                        </p>
                    @else
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
                    @endif
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
    <!-- ======================================================
        SEAPORT & MARINE
    ======================================================= -->

    <div class="tab-panel" id="tab-border-seaport">
            @php
$seaportStates = [

    'Lagos' => [ 'Tin Can Shift', 'Tin Can Jetty', 'Apapa Shift', 'Apapa Jetty', 'Ladol Free Zone', 'Marine Jetty', 'Badagry Patrol Base', 'Snake Island Free Zone' ],

    'Delta' => [ 'Sapele/Warri Jetty', 'Warri Jetty', 'Warri Marine Patrol Base', 'Koko/Sapele Seaport' ],

    'Cross River' => [ 'Calabar Marine Patrol Unit' ],

    'Rivers' => ['NPA Control Post', 'Onne Seaport', 'NPA Jetty'],
    'Ondo' => ['Igbokoda'], 'Adamawa' => ['Adamawa Marine Patrol Unit'],
    'Kebbi' => ['Dole Kaina Marine Patrol','Yauri Marine Patrol'],
    'Bayelsa' => ['Government Jetty'], 'Ogun' => ['Akere Marine'],
    'Akwa Ibom' => [
        'Oron',
        'Ibeno',
        'Ibaka',
        'Ebughu'
    ]
];
@endphp


<div class="redas-card mb-4">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-ship"></i>
            </div>
            4. Seaport &amp; Marine
        </div>
    </div>
</div>


@foreach($seaportStates as $state => $ports)

@php
    $seaportStateSlug = Str::slug($state);
@endphp

<div class="redas-card mb-4">

    <div class="card-head">

        <div class="card-head-title">

            <div class="card-head-icon"
                 style="background:#DCFCE7;color:#166534;">

                <i class="fas fa-anchor"></i>

            </div>

            {{ strtoupper($state) }} STATE

        </div>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="nis-table seaport-table">

                <thead>

                    <tr>

                        <th rowspan="2">S/N</th>

                        <th rowspan="2">Seaport / Marine Base</th>

                        <th colspan="3">Passenger Arrival</th>

                        <th colspan="3">Passenger Departure</th>

                        <th colspan="3">Crew Arrival</th>

                        <th colspan="3">Crew Departure</th>

                        <th colspan="2">Boat</th>

                    </tr>

                    <tr>

                        <th>M</th>
                        <th>F</th>
                        <th>Total</th>

                        <th>M</th>
                        <th>F</th>
                        <th>Total</th>

                        <th>M</th>
                        <th>F</th>
                        <th>Total</th>

                        <th>M</th>
                        <th>F</th>
                        <th>Total</th>

                        <th>Arrival</th>
                        <th>Departure</th>

                    </tr>

                </thead>

                <tbody>

                @foreach($ports as $index => $port)

                <tr>

                    <td>{{ $index + 1 }}</td>

                    <td>{{ $port }}</td>

                    {{-- Passenger Arrival --}}

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][passenger_arrival_male]"
                               class="ni passenger-arrival-male">
                    </td>

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][passenger_arrival_female]"
                               class="ni passenger-arrival-female">
                    </td>

                    <td>
                        <input type="number"
                               class="ni passenger-arrival-total"
                               readonly>
                    </td>

                    {{-- Passenger Departure --}}

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][passenger_departure_male]"
                               class="ni passenger-departure-male">
                    </td>

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][passenger_departure_female]"
                               class="ni passenger-departure-female">
                    </td>

                    <td>
                        <input type="number"
                               class="ni passenger-departure-total"
                               readonly>
                    </td>

                    {{-- Crew Arrival --}}

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][crew_arrival_male]"
                               class="ni crew-arrival-male">
                    </td>

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][crew_arrival_female]"
                               class="ni crew-arrival-female">
                    </td>

                    <td>
                        <input type="number"
                               class="ni crew-arrival-total"
                               readonly>
                    </td>

                    {{-- Crew Departure --}}

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][crew_departure_male]"
                               class="ni crew-departure-male">
                    </td>

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][crew_departure_female]"
                               class="ni crew-departure-female">
                    </td>

                    <td>
                        <input type="number"
                               class="ni crew-departure-total"
                               readonly>
                    </td>

                    {{-- Boat Arrival --}}

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][boat_arrival]"
                               class="ni boat-arrival">
                    </td>

                    {{-- Boat Departure --}}

                    <td>
                        <input type="number"
                               min="0"
                               value="0"
                               name="border[seaport][{{ $seaportStateSlug }}][{{ $index }}][boat_departure]"
                               class="ni boat-departure">
                    </td>

                </tr>

                @endforeach
                {{-- ============================
                    STATE TOTAL
                ============================= --}}

                <tr class="state-total bg-light fw-bold">

                    <th colspan="2">
                        STATE TOTAL
                    </th>

                    {{-- Passenger Arrival --}}
                    <td>
                        <input type="number"
                               class="ni total-arrival-male"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               class="ni total-arrival-female"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               class="ni total-arrival"
                               readonly>
                    </td>

                    {{-- Passenger Departure --}}
                    <td>
                        <input type="number"
                               class="ni total-departure-male"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               class="ni total-departure-female"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               class="ni total-departure"
                               readonly>
                    </td>

                    {{-- Crew Arrival --}}
                    <td>
                        <input type="number"
                               class="ni total-crew-arrival-male"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               class="ni total-crew-arrival-female"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               class="ni total-crew-arrival"
                               readonly>
                    </td>

                    {{-- Crew Departure --}}
                    <td>
                        <input type="number"
                               class="ni total-crew-departure-male"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               class="ni total-crew-departure-female"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               class="ni total-crew-departure"
                               readonly>
                    </td>

                    {{-- Boat --}}
                    <td>
                        <input type="number"
                               class="ni total-boat-arrival"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               class="ni total-boat-departure"
                               readonly>
                    </td>

                </tr>

                </tbody>

            </table>

        </div>


        {{-- ==========================================
            PASSENGER SUMMARY
        =========================================== --}}

        <div class="row mt-3">

            <div class="col-md-6">

                <div class="alert alert-success mb-0">

                    <div class="d-flex justify-content-between">

                        <strong>
                            <i class="fas fa-users"></i>
                            Passenger Arrival
                        </strong>

                        <span class="summary-arrival fw-bold">
                            0
                        </span>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="alert alert-primary mb-0">

                    <div class="d-flex justify-content-between">

                        <strong>
                            <i class="fas fa-plane-departure"></i>
                            Passenger Departure
                        </strong>

                        <span class="summary-departure fw-bold">
                            0
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================
            CREW SUMMARY
        =========================================== --}}

        <div class="row mt-3">

            <div class="col-md-6">

                <div class="alert alert-warning mb-0">

                    <div class="d-flex justify-content-between">

                        <strong>
                            <i class="fas fa-user-shield"></i>
                            Crew Arrival
                        </strong>

                        <span class="summary-crew-arrival fw-bold">
                            0
                        </span>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="alert alert-danger mb-0">

                    <div class="d-flex justify-content-between">

                        <strong>
                            <i class="fas fa-user-shield"></i>
                            Crew Departure
                        </strong>

                        <span class="summary-crew-departure fw-bold">
                            0
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================
            BOAT SUMMARY
        =========================================== --}}

        <div class="row mt-3">

            <div class="col-md-6">

                <div class="alert alert-info mb-0">

                    <div class="d-flex justify-content-between">

                        <strong>
                            <i class="fas fa-ship"></i>
                            Boat Arrival
                        </strong>

                        <span class="summary-boat-arrival fw-bold">
                            0
                        </span>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="alert alert-secondary mb-0">

                    <div class="d-flex justify-content-between">

                        <strong>
                            <i class="fas fa-ship"></i>
                            Boat Departure
                        </strong>

                        <span class="summary-boat-departure fw-bold">
                            0
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endforeach

    <div class="border-actions">
        <button type="button" class="btn-nis btn-ghost border-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
        <div class="border-actions-center">
            <button type="button" class="btn-nis btn-outline-nis border-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
        </div>
        <button type="button" class="btn-nis btn-primary-nis border-next-btn">Next <i class="fas fa-arrow-right"></i></button>
    </div>

    </div>



    <!-- ======================================================
        INTERNATIONAL AIRPORTS
    ======================================================= -->

    <div class="tab-panel" id="tab-border-airports">

            @php

$internationalAirports = [

    'Murtala Mohammed International Airport' => 'Lagos',

    'Nnamdi Azikiwe International Airport, Abuja' => 'Abuja',

    'Mallam Aminu Kano International Airport' => 'Kano',

    'Port Harcourt International Airport' => 'Rivers',

    'Akanu Ibiam International Airport, Enugu' => 'Enugu'

];

@endphp


<div class="redas-card mb-4">

    <div class="card-head">

        <div class="card-head-title">

            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">

                <i class="fas fa-plane"></i>

            </div>

            5. Int. Airports

        </div>

    </div>

</div>


@foreach($internationalAirports as $airport => $state)

@php
    $airportSlug = Str::slug($airport);
@endphp

<div class="redas-card mb-4">

    <div class="card-head">

        <div class="card-head-title">

            <div class="card-head-icon"
                 style="background:#DBEAFE;color:#1D4ED8;">

                <i class="fas fa-plane-departure"></i>

            </div>

            {{ strtoupper($airport) }}

        </div>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="nis-table airport-table">

                    <thead>

                        <tr>

                            <th rowspan="2" style="width:60px;">S/N</th>

                            <th rowspan="2" style="min-width:320px;">
                                Movement Category
                            </th>

                            <th colspan="2" class="text-center bg-success text-white">
                                Arrival
                            </th>

                            <th colspan="2" class="text-center bg-primary text-white">
                                Departure
                            </th>

                            <th rowspan="2" style="width:110px;">
                                Total
                            </th>

                        </tr>

                        <tr>

                            <th class="text-center">Male</th>

                            <th class="text-center">Female</th>

                            <th class="text-center">Male</th>

                            <th class="text-center">Female</th>

                        </tr>

                        </thead>

                <tbody>
                    @php
                    $categories = [

                        // =========================
                        // ARRIVAL
                        // =========================

                        [
                            'section' => 'Arrival',
                            'name' => 'Nigerians'
                        ],

                        [
                            'section' => 'Arrival',
                            'name' => 'Non-Nigerians'
                        ],

                        // =========================
                        // DEPARTURE
                        // =========================

                        [
                            'section' => 'Departure',
                            'name' => 'Nigerians Repatriated from Abroad (Regular Flight)'
                        ],

                        [
                            'section' => 'Departure',
                            'name' => 'Nigerians Deported from Abroad (Regular Flight)'
                        ],

                        [
                            'section' => 'Departure',
                            'name' => 'Non-Nigerians Repatriated'
                        ],

                        [
                            'section' => 'Departure',
                            'name' => 'Non-Nigerians Deported'
                        ],

                        [
                            'section' => 'Departure',
                            'name' => 'Nigerians Refused Entry Abroad'
                        ],

                        [
                            'section' => 'Departure',
                            'name' => 'Non-Nigerians Refused'
                        ],

                        [
                            'section' => 'Departure',
                            'name' => 'VIP Movement'
                        ],

                        [
                            'section' => 'Departure',
                            'name' => 'Nigerians Refused Departure'
                        ],

                        [
                            'section' => 'Departure',
                            'name' => 'Nigerians Repatriated on Special Flight'
                        ]

                    ];

                    @endphp

                    @php
$lastSection = '';
@endphp

@foreach($categories as $index => $category)

@if($lastSection != $category['section'])

<tr class="table-secondary">

    <th colspan="7">

        <i class="fas fa-folder-open me-2"></i>

        {{ strtoupper($category['section']) }}

    </th>

</tr>

@php
$lastSection = $category['section'];
@endphp

@endif

<tr>

    <td class="text-center">
        {{ $index + 1 }}
    </td>

    <td>
        {{ $category['name'] }}
    </td>

    {{-- Arrival Male --}}
    <td>
        <input type="number"
               class="ni airport-arrival-male"
               name="border[airports][{{ $airportSlug }}][{{ $index }}][arrival_male]"
               value="0"
               min="0">
    </td>

    {{-- Arrival Female --}}
    <td>
        <input type="number"
               class="ni airport-arrival-female"
               name="border[airports][{{ $airportSlug }}][{{ $index }}][arrival_female]"
               value="0"
               min="0">
    </td>

    {{-- Departure Male --}}
    <td>
        <input type="number"
               class="ni airport-departure-male"
               name="border[airports][{{ $airportSlug }}][{{ $index }}][departure_male]"
               value="0"
               min="0">
    </td>

    {{-- Departure Female --}}
    <td>
        <input type="number"
               class="ni airport-departure-female"
               name="border[airports][{{ $airportSlug }}][{{ $index }}][departure_female]"
               value="0"
               min="0">
    </td>

    {{-- Total --}}
    <td>

        <input type="number"
               class="ni airport-row-total"
               readonly>

    </td>

</tr>

@endforeach


                {{-- =====================================
                    AIRPORT TOTAL
                 ====================================== --}}

                <tr class="airport-total bg-light fw-bold">

                    <th colspan="2">
                        AIRPORT TOTAL
                    </th>

                    {{-- Arrival Male --}}
                    <td>
                        <input type="number"
                               class="ni total-airport-arrival-male"
                               readonly>
                    </td>

                    {{-- Arrival Female --}}
                    <td>
                        <input type="number"
                               class="ni total-airport-arrival-female"
                               readonly>
                    </td>

                    {{-- Departure Male --}}
                    <td>
                        <input type="number"
                               class="ni total-airport-departure-male"
                               readonly>
                    </td>

                    {{-- Departure Female --}}
                    <td>
                        <input type="number"
                               class="ni total-airport-departure-female"
                               readonly>
                    </td>

                    {{-- Grand Total --}}
                    <td>
                        <input type="number"
                               class="ni total-airport-grand"
                               readonly>
                    </td>

                </tr>

                </tbody>

            </table>

        </div>




        {{-- Airport Summary --}}

             <div class="airport-summary mt-3">

                <div class="row g-3">

                    <div class="col-lg-4 col-md-6">

                        <div class="summary-box arrival">

                            <div class="summary-title">
                                <i class="fas fa-plane-arrival"></i>
                                Arrival
                            </div>

                            <div class="summary-value airport-summary-arrival">
                                0
                            </div>

                        </div>

                    </div>

                    <div class="col-lg-4 col-md-6">

                        <div class="summary-box departure">

                            <div class="summary-title">
                                <i class="fas fa-plane-departure"></i>
                                Departure
                            </div>

                            <div class="summary-value airport-summary-departure">
                                0
                            </div>

                        </div>

                    </div>

                    <div class="col-lg-4 col-md-12">

                        <div class="summary-box total">

                            <div class="summary-title">
                                <i class="fas fa-chart-bar"></i>
                                Grand Total
                            </div>

                            <div class="summary-value airport-summary-grand">
                                0
                            </div>

                        </div>

                    </div>

                </div>

            </div>

                </div>

            </div>

            @endforeach

    <div class="border-actions">
        <button type="button" class="btn-nis btn-ghost border-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
        <div class="border-actions-center">
            <button type="button" class="btn-nis btn-outline-nis border-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
        </div>
        <button type="button" class="btn-nis btn-primary-nis border-next-btn">Next <i class="fas fa-arrow-right"></i></button>
    </div>

    </div>



    <!-- ======================================================
        OFFSHORE
    ======================================================= -->

    <div class="tab-panel" id="tab-border-offshore">

            <div class="redas-card mb-4">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#DBEAFE;color:#1D4ED8;"><i class="fas fa-anchor"></i></div>
            6. Offshore
        </div>
        <div class="card-head-action">
            <button type="button" id="btnAddState" class="btn btn-primary btn-sm"><i class="fas fa-plus-circle"></i>Add State</button>
        </div>
    </div>
</div>
<div id="offshoreStates"></div>

<!-- State Card Template
     __ST__ is replaced with the state card index by the inline
     script when the template is cloned. -->
<template id="offshoreStateTemplate">
    <div class="redas-card offshore-card mb-4">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#DCFCE7;color:#166534;"><i class="fas fa-map-marker-alt"></i></div>
           <div class="offshore-state-wrapper"><label class="ni-label mb-1"></label>
                <select class="ni offshore-state" name="border[offshore][__ST__][state]"><option value=""><= Select State =></option></select>
            </div>
                <option value=""></option>
            </select>
        </div>
        <div>
            <button type="button" class="btn btn-success btn-sm btnAddTerminal"> <i class="fas fa-plus"></i>Terminal</button>
            <button type="button" class="btn btn-danger btn-sm btnRemoveState"> <i class="fas fa-trash"></i></button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="nis-table offshore-table">
                <thead>
                <tr>
                    <th style="width:70px;">S/N</th>
                    <th>Terminal Name</th>
                    <th style="width:150px;">Tankers</th>
                    <th style="width:150px;">Crew</th>
                    <th style="width:80px;">Action</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="2">STATE TOTAL</th>
                        <th><input type="number" class="ni totalTankers" readonly></th>
                        <th><input type="number" class="ni totalCrew" readonly></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</template>

<!-- Terminal Row Template
     __ST__ is replaced with the owning state card index by the
     inline script when a row is added. -->
<template id="terminalTemplate">

<tr>

    <td class="sn"></td>

    <td>

        <input
            type="text"
            class="ni terminalName"
            name="border[offshore][__ST__][rows][terminal][]"
            placeholder="Terminal Name">

    </td>

    <td>

        <input
            type="number"
            class="ni tankerCount"
            name="border[offshore][__ST__][rows][tankers][]"
            value="0"
            min="0">

    </td>

    <td>

        <input
            type="number"
            class="ni crewCount"
            name="border[offshore][__ST__][rows][crew][]"
            value="0"
            min="0">

    </td>

    <td class="text-center">

        <button
            type="button"
            class="btn btn-danger btn-sm btnRemoveTerminal">

            <i class="fas fa-trash"></i>

        </button>

    </td>

</tr>

</template>

<!-- Grand Total Card -->
<div class="redas-card">

    <div class="card-head">

        <div class="card-head-title">

            <div class="card-head-icon"
                 style="background:#EFF6FF;color:#2563EB;">

                <i class="fas fa-chart-bar"></i>

            </div>

            Offshore Grand Total

        </div>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <label>Total Tankers</label>

                <input
                    type="number"
                    id="grandTankers"
                    class="ni"
                    readonly>

            </div>

            <div class="col-md-6">

                <label>Total Crew</label>

                <input
                    type="number"
                    id="grandCrew"
                    class="ni"
                    readonly>

            </div>

        </div>

    </div>

</div>

    <div class="border-actions">
        <button type="button" class="btn-nis btn-ghost border-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
        <div class="border-actions-center">
            <button type="button" class="btn-nis btn-outline-nis border-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
        </div>
        <button type="button" class="btn-nis btn-primary-nis border-next-btn">Next <i class="fas fa-arrow-right"></i></button>
    </div>

    </div>



    <!-- ======================================================
        GENERAL REPORTS
    ======================================================= -->

    <div class="tab-panel" id="tab-border-comments">

                    <div class="redas-card" style="margin-bottom:14px;" id="generalReport">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                            <i class="fas fa-comments"></i>
                        </div>
                        7. General Reports
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div class="fg"><label>Security Report</label><textarea name="border[general][security]" class="ni" rows="4" placeholder="Security situation and incidents during the reporting period..."></textarea></div>
                        <div class="fg"><label>Other Reports</label><textarea name="border[general][other]" class="ni" rows="4" placeholder="Any other relevant operational reports..."></textarea></div>
                        <div class="fg"><label>Challenges</label><textarea name="border[general][challenges]" class="ni" rows="4" placeholder="Challenges encountered during the reporting period..."></textarea></div>
                        <div class="fg"><label>Recommendations / Way Forward</label><textarea name="border[general][recommendations]" class="ni" rows="4" placeholder="Recommended actions and way forward..."></textarea></div>
                    </div>
                    <div class="fg" style="margin-top:4px;"><label>Conclusion</label><textarea name="border[general][conclusion]" class="ni" rows="3" placeholder="Concluding remarks..."></textarea></div>
                </div>
            </div>

    <div class="border-actions">
        <button type="button" class="btn-nis btn-ghost border-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
        <div class="border-actions-center">
            <button type="button" class="btn-nis btn-outline-nis border-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
        </div>
        <button type="button" class="btn-nis btn-primary-nis border-next-btn">Next <i class="fas fa-arrow-right"></i></button>
    </div>

    </div>

</div> {{-- /.tab-content-wrapper --}}


<script>
(function () {
    "use strict";

    /**************************************************************************
     * REDAS BORDER MANAGEMENT CALCULATIONS
     *
     * NOTE: Tab switching is handled centrally in partials/footer.blade.php
     * on the standalone page (and by the combined state form's own scoped
     * handler there), so it is intentionally NOT duplicated here.
     *
     * Every lookup is scoped to this directorate's panel (#dir-border) when
     * the partial is embedded in the combined state form, so unprefixed
     * classes like .total / .data-row / .sn cannot leak into other panels.
     * On the standalone page #dir-border does not exist and BORDER_ROOT
     * falls back to the whole document (behaviour unchanged).
     **************************************************************************/

    var BORDER_ROOT = document.getElementById('dir-border') || document;

    function bq(selector) { return BORDER_ROOT.querySelector(selector); }
    function bqa(selector) { return BORDER_ROOT.querySelectorAll(selector); }
    function inScope(target) {
        return BORDER_ROOT === document || BORDER_ROOT.contains(target);
    }

    var landBorderStates = @json($states);

    /* Counters used to give cloned command/state cards a stable index so
       their inputs submit as border[nationality][<i>][...] / border[offshore][<i>][...]. */
    var borderCommandIndex = 0;
    var borderOffshoreIndex = 0;

    function assignCloneNames(fragment, placeholder, index) {
        fragment.querySelectorAll('[name*="' + placeholder + '"]').forEach(function (field) {
            field.name = field.name.split(placeholder).join(index);
        });
    }

    /**************************************************************************
     * Initial calculation
     **************************************************************************/

    document.addEventListener("DOMContentLoaded", function () {

        calculateStaffStrength();
        calculateLandBorder();
        calculateSeaport();
        calculateAirportMovement();

    });

    /**************************************************************************
     * Auto Calculate on Input (consolidated)
     **************************************************************************/

    document.addEventListener("input", function (e) {

        if (!inScope(e.target)) return;

        if (e.target.closest(".staff-table")) {
            calculateStaffStrength();
        }

        if (e.target.closest(".land-border-table")) {
            calculateLandBorder();
        }

        if (e.target.closest(".seaport-table")) {
            calculateSeaport();
        }

        if (e.target.closest(".airport-table")) {
            calculateAirportMovement();
        }

        if (
            e.target.classList.contains("tankerCount") ||
            e.target.classList.contains("crewCount")
        ) {
            const offshoreCard = e.target.closest(".offshore-card");
            calculateStateTotals(offshoreCard);
            calculateGrandTotals();
        }

        /*
         * Nationality rows — scoped to rows inside .nationality-table so
         * keystrokes in other tables no longer throw on unguarded lookups.
         */
        const nationalityRow = e.target.closest(".nationality-table tbody tr");
        if (nationalityRow) {
            calculateNationalityRow(nationalityRow);
            updateCommandTotals(
                nationalityRow.closest(".nationality-card")
            );
        }

    });

    /**************************************************************************
     * Staff Strenght
     **************************************************************************/
    function calculateStaffStrength() {

        bqa(".staff-table").forEach(function(table){

            let male=0;
            let female=0;

            table.querySelectorAll("tbody tr").forEach(function(row){

                if(row.classList.contains("total-row")) return;

                let m=parseInt(row.querySelector(".male").value)||0;
                let f=parseInt(row.querySelector(".female").value)||0;

                row.querySelector(".total").value=m+f;

                male+=m;
                female+=f;

            });

            bq("#grandMale").value=male;
            bq("#grandFemale").value=female;
            bq("#grandTotal").value=male+female;

        });

    }
    /**************************************************************************
     * Land Border
     **************************************************************************/
    function calculateLandBorder(){

    bqa(".land-border-table").forEach(function(table){

    let arrivalMale=0;
    let arrivalFemale=0;

    let departureMale=0;
    let departureFemale=0;

    table.querySelectorAll("tbody tr").forEach(function(row){

    if(row.classList.contains("total-row")) return;

    let am=parseInt(row.querySelector(".arrival-male").value)||0;
    let af=parseInt(row.querySelector(".arrival-female").value)||0;

    let dm=parseInt(row.querySelector(".departure-male").value)||0;
    let df=parseInt(row.querySelector(".departure-female").value)||0;

    row.querySelector(".arrival-total").value=am+af;
    row.querySelector(".departure-total").value=dm+df;

    arrivalMale+=am;
    arrivalFemale+=af;

    departureMale+=dm;
    departureFemale+=df;

    });

    const total=table.querySelector(".total-row").querySelectorAll("input");

    total[0].value=arrivalMale;
    total[1].value=arrivalFemale;
    total[2].value=arrivalMale+arrivalFemale;

    total[3].value=departureMale;
    total[4].value=departureFemale;
    total[5].value=departureMale+departureFemale;

    });

    }
    /**************************************************************************
     * ACTIVITIES OF THE SERVICE AT THE SEAPORT & MARINE BASE
     **************************************************************************/
    function num(input){
        return parseInt(input?.value || 0) || 0;
    }

    function calculateSeaport(){

        bqa(".seaport-table").forEach(function(table){

            let totals = {

                passengerArrivalMale:0,
                passengerArrivalFemale:0,

                passengerDepartureMale:0,
                passengerDepartureFemale:0,

                crewArrivalMale:0,
                crewArrivalFemale:0,

                crewDepartureMale:0,
                crewDepartureFemale:0,

                boatArrival:0,
                boatDeparture:0

            };

            table.querySelectorAll("tbody tr").forEach(function(row){

                if(row.classList.contains("state-total")) return;

                //-------------------------------------
                // Passenger Arrival
                //-------------------------------------

                let paMale = num(row.querySelector(".passenger-arrival-male"));
                let paFemale = num(row.querySelector(".passenger-arrival-female"));

                row.querySelector(".passenger-arrival-total").value =
                    paMale + paFemale;

                totals.passengerArrivalMale += paMale;
                totals.passengerArrivalFemale += paFemale;


                //-------------------------------------
                // Passenger Departure
                //-------------------------------------

                let pdMale = num(row.querySelector(".passenger-departure-male"));
                let pdFemale = num(row.querySelector(".passenger-departure-female"));

                row.querySelector(".passenger-departure-total").value =
                    pdMale + pdFemale;

                totals.passengerDepartureMale += pdMale;
                totals.passengerDepartureFemale += pdFemale;


                //-------------------------------------
                // Crew Arrival
                //-------------------------------------

                let caMale = num(row.querySelector(".crew-arrival-male"));
                let caFemale = num(row.querySelector(".crew-arrival-female"));

                row.querySelector(".crew-arrival-total").value =
                    caMale + caFemale;

                totals.crewArrivalMale += caMale;
                totals.crewArrivalFemale += caFemale;


                //-------------------------------------
                // Crew Departure
                //-------------------------------------

                let cdMale = num(row.querySelector(".crew-departure-male"));
                let cdFemale = num(row.querySelector(".crew-departure-female"));

                row.querySelector(".crew-departure-total").value =
                    cdMale + cdFemale;

                totals.crewDepartureMale += cdMale;
                totals.crewDepartureFemale += cdFemale;


                //-------------------------------------
                // Boat
                //-------------------------------------

                totals.boatArrival +=
                    num(row.querySelector(".boat-arrival"));

                totals.boatDeparture +=
                    num(row.querySelector(".boat-departure"));

            });

            //-----------------------------------------
            // STATE TOTAL ROW
            //-----------------------------------------

            const totalRow = table.querySelector(".state-total");

            totalRow.querySelector(".total-arrival-male").value =
                totals.passengerArrivalMale;

            totalRow.querySelector(".total-arrival-female").value =
                totals.passengerArrivalFemale;

            totalRow.querySelector(".total-arrival").value =
                totals.passengerArrivalMale +
                totals.passengerArrivalFemale;


            totalRow.querySelector(".total-departure-male").value =
                totals.passengerDepartureMale;

            totalRow.querySelector(".total-departure-female").value =
                totals.passengerDepartureFemale;

            totalRow.querySelector(".total-departure").value =
                totals.passengerDepartureMale +
                totals.passengerDepartureFemale;


            totalRow.querySelector(".total-crew-arrival-male").value =
                totals.crewArrivalMale;

            totalRow.querySelector(".total-crew-arrival-female").value =
                totals.crewArrivalFemale;

            totalRow.querySelector(".total-crew-arrival").value =
                totals.crewArrivalMale +
                totals.crewArrivalFemale;


            totalRow.querySelector(".total-crew-departure-male").value =
                totals.crewDepartureMale;

            totalRow.querySelector(".total-crew-departure-female").value =
                totals.crewDepartureFemale;

            totalRow.querySelector(".total-crew-departure").value =
                totals.crewDepartureMale +
                totals.crewDepartureFemale;


            totalRow.querySelector(".total-boat-arrival").value =
                totals.boatArrival;

            totalRow.querySelector(".total-boat-departure").value =
                totals.boatDeparture;


            //-----------------------------------------
            // SUMMARY CARDS
            //-----------------------------------------

            const card = table.closest(".redas-card");

            card.querySelector(".summary-arrival").textContent =
                totals.passengerArrivalMale +
                totals.passengerArrivalFemale;

            card.querySelector(".summary-departure").textContent =
                totals.passengerDepartureMale +
                totals.passengerDepartureFemale;

            card.querySelector(".summary-crew-arrival").textContent =
                totals.crewArrivalMale +
                totals.crewArrivalFemale;

            card.querySelector(".summary-crew-departure").textContent =
                totals.crewDepartureMale +
                totals.crewDepartureFemale;

            card.querySelector(".summary-boat-arrival").textContent =
                totals.boatArrival;

            card.querySelector(".summary-boat-departure").textContent =
                totals.boatDeparture;

        });

    }


    /**************************************************************
     * PASSENGER MOVEMENT ACROSS INTERNATIONAL AIRPORTS
     **************************************************************/

    function numberValue(input){
        return parseInt(input?.value || 0) || 0;
    }

    function calculateAirportMovement(){

        bqa(".airport-table").forEach(function(table){

            let arrivalMale = 0;
            let arrivalFemale = 0;

            let departureMale = 0;
            let departureFemale = 0;

            table.querySelectorAll("tbody tr").forEach(function(row){

                if(row.classList.contains("airport-total"))
                    return;

                let arrivalMaleInput =
                    row.querySelector(".airport-arrival-male");

                if(!arrivalMaleInput)
                    return;

                let arrivalFemaleInput =
                    row.querySelector(".airport-arrival-female");

                let departureMaleInput =
                    row.querySelector(".airport-departure-male");

                let departureFemaleInput =
                    row.querySelector(".airport-departure-female");

                let totalInput =
                    row.querySelector(".airport-row-total");

                let am = numberValue(arrivalMaleInput);
                let af = numberValue(arrivalFemaleInput);

                let dm = numberValue(departureMaleInput);
                let df = numberValue(departureFemaleInput);

                //---------------------------------------
                // Row Total
                //---------------------------------------

                totalInput.value = am + af + dm + df;

                //---------------------------------------
                // Running Totals
                //---------------------------------------

                arrivalMale += am;
                arrivalFemale += af;

                departureMale += dm;
                departureFemale += df;

            });

            //---------------------------------------
            // Airport Total Row
            //---------------------------------------

            const totalRow = table.querySelector(".airport-total");

            totalRow.querySelector(".total-airport-arrival-male").value =
                arrivalMale;

            totalRow.querySelector(".total-airport-arrival-female").value =
                arrivalFemale;

            totalRow.querySelector(".total-airport-departure-male").value =
                departureMale;

            totalRow.querySelector(".total-airport-departure-female").value =
                departureFemale;

            totalRow.querySelector(".total-airport-grand").value =
                arrivalMale +
                arrivalFemale +
                departureMale +
                departureFemale;

            //---------------------------------------
            // Airport Summary
            //---------------------------------------

            const card = table.closest(".redas-card");

            if(card){

                card.querySelector(".airport-summary-arrival").textContent =
                    arrivalMale + arrivalFemale;

                card.querySelector(".airport-summary-departure").textContent =
                    departureMale + departureFemale;

                card.querySelector(".airport-summary-grand").textContent =
                    arrivalMale +
                    arrivalFemale +
                    departureMale +
                    departureFemale;

            }

        });

    }


    /**************************************************************************
     * OFFSHORE ACTIVITIES
     **************************************************************************/
    var offshoreData = {

        "Akwa Ibom": [
            "BOP (QIT)",
            "YOHO",
            "ODUDU",
            "EBOK",
            "ATAN"
        ],

        "Bayelsa": [
            "AGBAMI",
            "BONGA/EA",
            "TULJA",
            "PENNINGTON",
            "BRASS"
        ],

        "Delta": [
            "Escravos",
            "Forcados"
        ],

        "Rivers": [
            "Bonny",
            "Onne",
            "Otakikpo",
            "Okrika Jetty",
            "Akpo Field",
            "Egina Field",
            "Agbami Field"
        ],

        "Lagos": [
            "LADOL",
            "EA Terminal",
            "Abo Terminal",
            "Bonga Terminal",
            "Erha Terminal"
        ]

    };

    document.addEventListener("DOMContentLoaded", function () {

        const btnAddState = bq("#btnAddState");
        const offshoreStates = bq("#offshoreStates");
        const stateTemplate = bq("#offshoreStateTemplate");

        if (!btnAddState || !offshoreStates || !stateTemplate) {
            console.error("Offshore elements not found.");
            return;
        }

        btnAddState.addEventListener("click", function () {

            const clone = stateTemplate.content.cloneNode(true);

            const select = clone.querySelector(".offshore-state");

            Object.keys(offshoreData).forEach(function(state){

                const option = document.createElement("option");

                option.value = state;
                option.textContent = state;

                select.appendChild(option);

            });

            const index = borderOffshoreIndex++;
            assignCloneNames(clone, "__ST__", index);

            const card = clone.querySelector(".offshore-card");
            if (card) card.dataset.stIndex = index;

            offshoreStates.appendChild(clone);

        });

    });

    // offshoreData
    function createTerminalRow(terminalName, stIndex) {

        const template = bq("#terminalTemplate");

        const row = template.content.cloneNode(true);

        row.querySelector(".terminalName").value = terminalName;

        assignCloneNames(row, "__ST__", stIndex || 0);

        return row;

    }

    // LOAD STATE

    document.addEventListener("change", function (e) {

        if (!inScope(e.target)) return;
        if (!e.target.classList.contains("offshore-state")) return;

        const select = e.target;

        const state = select.value;

        if (state === "") return;

        // Check if this state already exists
        let duplicate = false;

        bqa(".offshore-state").forEach(function(item){

            if(item !== select && item.value === state){

                duplicate = true;

            }

        });

        if(duplicate){

            alert(state + " has already been added.");

            select.value = "";

            return;

        }

        const card = select.closest(".offshore-card");

        const tbody = card.querySelector("tbody");

        tbody.innerHTML = "";

        offshoreData[state].forEach(function(terminal){

            tbody.appendChild(createTerminalRow(terminal, card.dataset.stIndex));

        });

        updateSerialNumbers(card);

        calculateStateTotals(card);

        calculateGrandTotals();

    });

    // Serial Number Function

    function updateSerialNumbers(card){

        card.querySelectorAll("tbody tr").forEach(function(row,index){

            row.querySelector(".sn").textContent = index + 1;

        });

    }


    // ADD TERMINAL

    document.addEventListener("click", function (e) {

        if (!inScope(e.target)) return;
        if (!e.target.closest(".btnAddTerminal")) return;

        const card = e.target.closest(".offshore-card");

        const tbody = card.querySelector("tbody");

        tbody.appendChild(createTerminalRow("", card.dataset.stIndex));

        updateSerialNumbers(card);

    });



    //  * REMOVE TERMINAL

    document.addEventListener("click", function (e) {

        if (!inScope(e.target)) return;
        if (!e.target.closest(".btnRemoveTerminal")) return;

        const row = e.target.closest("tr");

        const card = e.target.closest(".offshore-card");

        row.remove();

        updateSerialNumbers(card);

        calculateStateTotals(card);

        calculateGrandTotals();

    });

    //  * REMOVE STATE


    document.addEventListener("click", function (e) {

        if (!inScope(e.target)) return;
        if (!e.target.closest(".btnRemoveState")) return;

        if (!confirm("Remove this state?")) return;

        e.target.closest(".offshore-card").remove();

        calculateGrandTotals();

    });

    //  * STATE TOTALS

    function calculateStateTotals(card){

        let tankers = 0;

        let crew = 0;

        card.querySelectorAll("tbody tr").forEach(function(row){

            tankers += Number(row.querySelector(".tankerCount").value) || 0;

            crew += Number(row.querySelector(".crewCount").value) || 0;

        });

        card.querySelector(".totalTankers").value = tankers;

        card.querySelector(".totalCrew").value = crew;

    }


    //  * GRAND TOTAL

    function calculateGrandTotals(){

        let tankers = 0;

        let crew = 0;

        bqa(".offshore-card").forEach(function(card){

            tankers += Number(card.querySelector(".totalTankers").value) || 0;

            crew += Number(card.querySelector(".totalCrew").value) || 0;

        });

        bq("#grandTankers").value = tankers;

        bq("#grandCrew").value = crew;

    }


    /*****************************************************************
     * LAND BORDER RETURNS BY NATIONALITY
     *****************************************************************/

    // =========================================
    // NATIONALITY LIST
    // =========================================

    var nationalityList = [
        "Nigeria", "Benin", "Burkina Faso", "Cameroon", "Cape Verde",
        "Central African Republic", "Chad", "Congo", "Côte d'Ivoire",
        "DR Congo", "Egypt", "Equatorial Guinea", "Eritrea",
        "Eswatini", "Ethiopia", "Gabon", "Gambia", "Ghana",
        "Guinea", "Guinea-Bissau", "Kenya", "Liberia",
        "Libya", "Mali", "Morocco", "Niger", "Senegal",
        "Sierra Leone", "South Africa", "Sudan", "Togo",
        "Tunisia", "United Kingdom", "United States",
        "Canada", "France", "Germany", "Italy", "Spain",
        "Turkey", "China", "India", "Pakistan",
        "Japan", "South Korea", "Brazil", "Russia"
    ];

    // =========================================
    // INITIALIZE MODULE
    // =========================================

    document.addEventListener("DOMContentLoaded", function () {

        const btnAddCommand = bq("#btnAddCommand");
        const commandContainer = bq("#commandContainer");
        const commandTemplate = bq("#commandTemplate");

        if (!btnAddCommand || !commandContainer || !commandTemplate) return;

        btnAddCommand.addEventListener("click", function () {

        const clone = commandTemplate.content.cloneNode(true);

        const stateSelect = clone.querySelector(".command-state");

        Object.keys(landBorderStates).forEach(function(state){

            const option = document.createElement("option");

            option.value = state;
            option.textContent = state;

            stateSelect.appendChild(option);

        });

        const index = borderCommandIndex++;
        assignCloneNames(clone, "__CMD__", index);

        const card = clone.querySelector(".nationality-card");
        if (card) card.dataset.cmdIndex = index;

        commandContainer.appendChild(clone);
        updateOverallTotals();

        });

    });

    // =========================================
    // LOAD CONTROL POSTS (+ duplicate state guard)
    // =========================================

    document.addEventListener("change", function(e){

        if (!inScope(e.target)) return;
        if(!e.target.classList.contains("command-state")) return;

        const select = e.target;
        const state = select.value;

        const card = select.closest(".nationality-card");

        const controlPost = card.querySelector(".control-post");

        controlPost.innerHTML =
            '<option value="">Select Control Post</option>';

        if(state === "") return;

        // Prevent duplicate states
        let duplicate = false;

        bqa(".command-state").forEach(function(item){

            if(item !== select && item.value === state){

                duplicate = true;

            }

        });

        if(duplicate){

            alert(state + " has already been added.");

            select.value = "";

            return;

        }

        if(!landBorderStates[state]) return;

        landBorderStates[state].forEach(function(post){

            const option = document.createElement("option");

            option.value = post;
            option.textContent = post;

            controlPost.appendChild(option);

        });

    });

    // =========================================
    // ADD NATIONALITY ROW
    // =========================================

    document.addEventListener("click", function(e){

        if (!inScope(e.target)) return;

        const button = e.target.closest(".btnAddNationalityRow");

        if(!button) return;

        const card = button.closest(".nationality-card");

        const tbody = card.querySelector("tbody");

        const template =
            bq("#nationalityRowTemplate");

        const clone =
            template.content.cloneNode(true);

        assignCloneNames(clone, "__CMD__", card.dataset.cmdIndex || 0);

        loadNationalityOptions(
            clone.querySelector(".nationality-select")
        );

        tbody.appendChild(clone);

        updateNationalitySerial(card);

        updateCommandTotals(card);

    });

    // =========================================
    // DELETE ROW
    // =========================================

    document.addEventListener("click", function(e){

        if (!inScope(e.target)) return;

        const button = e.target.closest(".btnRemoveNationality");

        if(!button) return;

        const card = button.closest(".nationality-card");

        button.closest("tr").remove();

        updateNationalitySerial(card);

        updateCommandTotals(card);

    });

    // =========================================
    // DELETE COMMAND
    // =========================================

    document.addEventListener("click", function(e){

        if (!inScope(e.target)) return;

        const button = e.target.closest(".btnRemoveCommand");

        if(!button) return;

        const card = button.closest(".nationality-card");

        card.remove();

        updateOverallTotals();

    });

    // =========================================
    // SERIAL NUMBERS
    // =========================================

    function updateNationalitySerial(card){

        card.querySelectorAll("tbody tr").forEach(function(row,index){

            row.querySelector(".sn").textContent = index + 1;

        });

    }

    // =========================================
    // LOAD NATIONALITIES
    // =========================================

    function loadNationalityOptions(select){

        select.innerHTML =
            '<option value="">Select Nationality</option>';

        nationalityList.forEach(function(country){

            const option =
                document.createElement("option");

            option.value = country;
            option.textContent = country;

            select.appendChild(option);

        });

    }

    // =========================================
    // ROW TOTALS
    // =========================================

    function calculateNationalityRow(row){

        const arrivalMale =
            Number(row.querySelector(".arrivalMale").value);

        const arrivalFemale =
            Number(row.querySelector(".arrivalFemale").value);

        const departureMale =
            Number(row.querySelector(".departureMale").value);

        const departureFemale =
            Number(row.querySelector(".departureFemale").value);

        const arrivalTotal =
            arrivalMale + arrivalFemale;

        const departureTotal =
            departureMale + departureFemale;

        const grandTotal =
            arrivalTotal + departureTotal;

        row.querySelector(".arrivalTotal").value =
            arrivalTotal;

        row.querySelector(".departureTotal").value =
            departureTotal;

        row.querySelector(".grandTotal").value =
            grandTotal;

    }

    // =========================================
    // FOOTER TOTALS
    // =========================================

    function updateCommandTotals(card){

        let arrivalMale = 0;
        let arrivalFemale = 0;

        let departureMale = 0;
        let departureFemale = 0;

        card.querySelectorAll("tbody tr").forEach(function(row){

            arrivalMale +=
                Number(row.querySelector(".arrivalMale").value);

            arrivalFemale +=
                Number(row.querySelector(".arrivalFemale").value);

            departureMale +=
                Number(row.querySelector(".departureMale").value);

            departureFemale +=
                Number(row.querySelector(".departureFemale").value);

        });

        const arrivalTotal =
            arrivalMale + arrivalFemale;

        const departureTotal =
            departureMale + departureFemale;

        const grandTotal =
            arrivalTotal + departureTotal;

        card.querySelector(".totalArrivalMale").value =
            arrivalMale;

        card.querySelector(".totalArrivalFemale").value =
            arrivalFemale;

        card.querySelector(".totalArrival").value =
            arrivalTotal;

        card.querySelector(".totalDepartureMale").value =
            departureMale;

        card.querySelector(".totalDepartureFemale").value =
            departureFemale;

        card.querySelector(".totalDeparture").value =
            departureTotal;

        card.querySelector(".totalGrand").value =
            grandTotal;

    }


    // =========================================
    // PREVENT DUPLICATE NATIONALITIES
    // =========================================

    document.addEventListener("change", function(e){

        if (!inScope(e.target)) return;
        if(!e.target.classList.contains("nationality-select")) return;

        const select = e.target;

        if(select.value === "") return;

        const card = select.closest(".nationality-card");

        let duplicate = false;

        card.querySelectorAll(".nationality-select").forEach(function(item){

            if(item !== select && item.value === select.value){

                duplicate = true;

            }

        });

        if(duplicate){

            alert(select.value + " has already been selected.");

            select.value = "";

        }

    });

    // =========================================
    // OVERALL TOTALS
    // =========================================

    function updateOverallTotals(){

        let arrivalMale = 0;
        let arrivalFemale = 0;

        let departureMale = 0;
        let departureFemale = 0;

        bqa(".nationality-card").forEach(function(card){

            arrivalMale += Number(card.querySelector(".totalArrivalMale")?.value || 0);

            arrivalFemale += Number(card.querySelector(".totalArrivalFemale")?.value || 0);

            departureMale += Number(card.querySelector(".totalDepartureMale")?.value || 0);

            departureFemale += Number(card.querySelector(".totalDepartureFemale")?.value || 0);

        });

        const arrivalTotal = arrivalMale + arrivalFemale;

        const departureTotal = departureMale + departureFemale;

        const grandTotal = arrivalTotal + departureTotal;

        const arrivalMaleInput = bq("#overallArrivalMale");
        const arrivalFemaleInput = bq("#overallArrivalFemale");
        const arrivalTotalInput = bq("#overallArrivalTotal");

        const departureMaleInput = bq("#overallDepartureMale");
        const departureFemaleInput = bq("#overallDepartureFemale");
        const departureTotalInput = bq("#overallDepartureTotal");

        const grandTotalInput = bq("#overallGrandTotal");

        if(arrivalMaleInput) arrivalMaleInput.value = arrivalMale;
        if(arrivalFemaleInput) arrivalFemaleInput.value = arrivalFemale;
        if(arrivalTotalInput) arrivalTotalInput.value = arrivalTotal;

        if(departureMaleInput) departureMaleInput.value = departureMale;
        if(departureFemaleInput) departureFemaleInput.value = departureFemale;
        if(departureTotalInput) departureTotalInput.value = departureTotal;

        if(grandTotalInput) grandTotalInput.value = grandTotal;

    }

    /**************************************************************************
     * Recalculate every section. Exposed under a single namespaced global so
     * the standalone wrapper's Preview builder can refresh totals before
     * rendering (the preview module lives in user.directorates.border).
     **************************************************************************/
    window.borderFormRecalc = function () {
        calculateStaffStrength();
        calculateLandBorder();
        calculateSeaport();
        calculateAirportMovement();
        calculateGrandTotals();
        updateOverallTotals();
    };

})();
</script>
