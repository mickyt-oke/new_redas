{{-- Visa & Residence Report Form --}}

@include('partials.header')

<main class="redas-content">

    <!-- Page header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-passport"></i>
               Visa & Residence Annual Report
            </h1>

            <p class="page-subtitle">
               Complete all applicable sections before submitting the Annual Report in accordance with the official NIS reporting template.
            </p>
        </div>

        <a href="{{ route('visa.dashboard') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>

    <!-- Status message -->
    @if(session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:18px;">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('visa.store') }}">

        @csrf

        <!-- General Information -->
        <div class="redas-card mb-3">

            <div class="card-head">

                <div class="card-head-title">

                    <div class="card-head-icon"
                        style="background:var(--gold-100);color:var(--gold-600);">

                        <i class="fas fa-file-signature"></i>

                    </div>

                    General Information

                </div>

            </div>

            <div class="card-body"
                style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:15px;">

                <div>

                    <label>Reporting Year</label>

                                    <select
                    name="report_year"
                    class="ni"
                    required>

                    @for($year = date('Y'); $year >= 2020; $year--)

                    <option value="{{ $year }}"
                        {{ old('report_year', date('Y')) == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>

                    @endfor

                    </select>

                </div>

                <div>

                    <label>Reporting Officer</label>

                    <input
                        type="text"
                        class="ni"
                        name="reporting_officer"
                        readonly
                        value="{{ auth()->user()->name }}">

                </div>

                <div>

                    <label>Directorate</label>

                    <input
                        type="text"
                        class="ni"
                        readonly
                        value="Visa & Residence Directorate">

                </div>

            </div>

        </div>

        <!-- Reporting Sections -->

        <div class="accordion" id="visaAccordion">

            @include('user.visa.partials.staff-strength')

            @include('user.visa.partials.e-migrant')

            @include('user.visa.partials.quota')

            @include('user.visa.partials.residence')

            @include('user.visa.partials.ftz')

            @include('user.visa.partials.cerpac')

            @include('user.visa.partials.visa')

            @include('user.visa.partials.ecowas')

            @include('user.visa.partials.african-affairs')

        </div>

        <!-- Submit -->

        <div class="redas-card mt-3">

            <div class="card-body"
                style="display:flex;justify-content:flex-end;gap:12px;">

                <a
                    href="{{ route('visa.dashboard') }}"
                    class="btn-nis btn-ghost">

                    Cancel

                </a>

                <button
                    type="submit"
                    class="btn-nis btn-primary-nis">

                    <i class="fas fa-paper-plane"></i>

                    Submit Annual Report

                </button>

            </div>

        </div>

    </form>

</main>

@include('partials.footer')