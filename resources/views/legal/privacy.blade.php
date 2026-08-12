<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Privacy Policy for NIS-REDAS platform usage.">
    <title>NIS-REDAS | Privacy Policy</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @include('partials.head-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA
    <style>
        body { background: #f8f9fa; }
        .legal-wrap { max-width: 900px; margin: 40px auto; padding: 0 16px; }
        .legal-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }
        .legal-title { margin-bottom: 6px; }
        .legal-updated { color: #6c757d; margin-bottom: 20px; }
        .legal-section { margin-bottom: 18px; }
        .legal-section h2 { font-size: 1.1rem; margin-bottom: 8px; }
    </style>
</head>
<body>

@include('partials.preloader')

    <main class="legal-wrap">
        <section class="legal-card" aria-labelledby="privacyTitle">
            <h1 id="privacyTitle" class="legal-title">Privacy Policy</h1>
            <p class="legal-updated">Last updated: {{ date('F j, Y') }}</p>

            <div class="legal-section">
                <h2>1. Information We Process</h2>
                <p>
                    REDAS processes account data (name, service number, email, role, location) and operational reporting data
                    submitted by authorised personnel. This may include aggregated statistics and, where necessary, limited personal data
                    required for official immigration service reporting.
                </p>
            </div>

            <div class="legal-section">
                <h2>2. Purpose and Lawful Basis of Processing</h2>
                <p>
                    Data is processed for official service delivery, operational oversight, compliance tracking, and internal reporting.
                    The lawful basis is the performance of a public task / official function of the Nigeria Immigration Service and,
                    where applicable, compliance with a legal obligation under Nigerian law and international data-protection standards.
                </p>
            </div>

            <div class="legal-section">
                <h2>3. Data Minimization</h2>
                <p>
                    Users must only enter data that is adequate, relevant, and limited to what is necessary for the stated purpose.
                    Unnecessary personal or sensitive data should not be uploaded, stored, or shared through the platform.
                </p>
            </div>

            <div class="legal-section">
                <h2>4. Access and Security</h2>
                <p>
                    Access is role-based and restricted to authorised users. Technical and administrative controls — including
                    encryption in transit, access logs, rate limiting, and regular review of permissions — are applied to protect
                    data confidentiality, integrity, and availability.
                </p>
            </div>

            <div class="legal-section">
                <h2>5. Retention</h2>
                <p>
                    Operational returns are retained for the period required by NIS archival and regulatory requirements
                    (typically up to seven years), after which they are anonymised or securely disposed of. Personal data is kept
                    only as long as necessary for the purposes for which it was collected or as required by law.
                </p>
            </div>

            <div class="legal-section">
                <h2>6. Data Subject Rights</h2>
                <p>
                    In line with the Nigeria Data Protection Regulation (NDPR) and GDPR principles, individuals have the right to:
                </p>
                <ul>
                    <li>Request access to their personal data;</li>
                    <li>Request rectification of inaccurate or incomplete data;</li>
                    <li>Request erasure or restriction of processing where applicable;</li>
                    <li>Object to processing in certain circumstances; and</li>
                    <li>Request data portability where technically feasible.</li>
                </ul>
                <p>
                    Requests should be submitted through the designated data-protection contact or system administrator.
                </p>
            </div>

            <div class="legal-section">
                <h2>7. International Transfers</h2>
                <p>
                    Personal data is processed within Nigeria. Any transfer outside Nigeria will only occur with appropriate safeguards
                    and in compliance with applicable data-protection law.
                </p>
            </div>

            <div class="legal-section">
                <h2>8. User Responsibilities</h2>
                <p>
                    Users must submit accurate information, protect their credentials, and handle system data in accordance with
                    internal confidentiality, security, and data governance directives. Sharing credentials or exporting data without
                    authorisation is prohibited.
                </p>
            </div>

            <div class="legal-section">
                <h2>9. Contact</h2>
                <p>
                    For privacy-related concerns, contact the system administrator or the designated privacy/security contact within
                    your command structure. For formal data-protection requests, email the NIS REDAS support desk or the Data
                    Protection Officer (DPO) where one has been appointed.
                </p>
            </div>

            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Back to Login</a>
        </section>
    </main>
</body>
</html>
