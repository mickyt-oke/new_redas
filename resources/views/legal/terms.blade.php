<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Terms and Conditions for NIS-REDAS platform access and usage.">
    <title>NIS-REDAS | Terms and Conditions</title>
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
        <section class="legal-card" aria-labelledby="termsTitle">
            <h1 id="termsTitle" class="legal-title">Terms and Conditions</h1>
            <p class="legal-updated">Last updated: {{ date('F j, Y') }}</p>

            <div class="legal-section">
                <h2>1. Purpose and Scope</h2>
                <p>
                    These Terms govern access to and use of the NIS REDAS platform. The system is intended
                    strictly for authorized internal users handling official reporting and archiving workflows.
                </p>
            </div>

            <div class="legal-section">
                <h2>2. Account Responsibility</h2>
                <p>
                    You are responsible for maintaining the confidentiality of your credentials and all actions
                    performed under your account. Unauthorized account sharing is prohibited.
                </p>
            </div>

            <div class="legal-section">
                <h2>3. Acceptable Use</h2>
                <p>
                    Users must submit accurate records, follow approved internal procedures, and comply with all
                    applicable operational directives and security requirements.
                </p>
            </div>

            <div class="legal-section">
                <h2>4. Data Handling</h2>
                <p>
                    Information in REDAS may include sensitive operational data. Access, processing, storage,
                    and sharing must follow approved confidentiality and data-protection controls.
                </p>
            </div>

            <div class="legal-section">
                <h2>5. Monitoring and Enforcement</h2>
                <p>
                    System activity may be monitored and audited. Violations may result in suspension or
                    termination of access and additional administrative actions.
                </p>
            </div>

            <div class="legal-section">
                <h2>6. Contact</h2>
                <p>
                    For clarification on these Terms, contact the platform administrator or your designated
                    ICT/security focal point.
                </p>
            </div>

            <a href="{{ route('register') }}" class="btn btn-outline-secondary">Back to Register</a>
        </section>
    </main>
</body>
</html>