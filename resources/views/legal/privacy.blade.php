<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Privacy Policy for NIS-REDAS platform usage.">
    <title>NIS-REDAS | Privacy Policy</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <main class="legal-wrap">
        <section class="legal-card" aria-labelledby="privacyTitle">
            <h1 id="privacyTitle" class="legal-title">Privacy Policy</h1>
            <p class="legal-updated">Last updated: {{ date('F j, Y') }}</p>

            <div class="legal-section">
                <h2>1. Information We Process</h2>
                <p>
                    REDAS processes account and operational reporting data required to provide dashboard,
                    submission, and archival functions for authorized personnel.
                </p>
            </div>

            <div class="legal-section">
                <h2>2. Purpose of Processing</h2>
                <p>
                    Data is processed for official service delivery, operational oversight, compliance tracking,
                    and internal reporting requirements.
                </p>
            </div>

            <div class="legal-section">
                <h2>3. Access and Security</h2>
                <p>
                    Access is role-based and restricted to authorized users. Technical and administrative controls
                    are applied to protect data confidentiality, integrity, and availability.
                </p>
            </div>

            <div class="legal-section">
                <h2>4. Retention</h2>
                <p>
                    Records are retained in line with approved operational, regulatory, and archival requirements.
                    Data may be retained longer where required for legal or compliance purposes.
                </p>
            </div>

            <div class="legal-section">
                <h2>5. User Responsibilities</h2>
                <p>
                    Users must submit accurate information and handle system data in accordance with internal
                    confidentiality, security, and data governance directives.
                </p>
            </div>

            <div class="legal-section">
                <h2>6. Contact</h2>
                <p>
                    For privacy-related concerns, contact the system administrator or designated privacy/security
                    contact within your command structure.
                </p>
            </div>

            <a href="{{ route('register') }}" class="btn btn-outline-secondary">Back to Register</a>
        </section>
    </main>
</body>
</html>