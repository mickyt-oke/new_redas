<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Page Not Found | NIS-REDAS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            color: #1f2937;
        }
        .card {
            background: #fff;
            max-width: 460px;
            width: 100%;
            margin: 24px;
            padding: 36px 32px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
            text-align: center;
        }
        .icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #eff6ff;
            color: #1e40af;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon svg { width: 32px; height: 32px; fill: currentColor; }
        .code {
            font-size: .85rem;
            font-weight: 700;
            color: #1e40af;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        h1 { font-size: 1.35rem; margin: 10px 0 8px; }
        p { font-size: .92rem; color: #4b5563; line-height: 1.5; }
        .actions { margin-top: 28px; display: flex; gap: 12px; justify-content: center; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: .88rem;
        }
        .btn-primary { background: #0f3d75; color: #fff; }
        .btn-secondary { background: #f3f4f6; color: #1f2937; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        </div>
        <div class="code">Error 404</div>
        <h1>Page not found</h1>
        <p>The page you are looking for may have been moved, renamed, or is temporarily unavailable. Please check the address or return to the dashboard.</p>
        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
        </div>
    </div>
</body>
</html>
