<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Access Denied | NIS-REDAS</title>
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
            background: #fee2e2;
            color: #991b1b;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon svg { width: 32px; height: 32px; fill: currentColor; }
        .code {
            font-size: .85rem;
            font-weight: 700;
            color: #991b1b;
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
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
        </div>
        <div class="code">Error 403</div>
        <h1>Access denied</h1>
        <p>You do not have permission to view this page or perform this action. If you believe this is a mistake, contact your administrator or sign in with an authorised account.</p>
        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            <a href="{{ route('login') }}" class="btn btn-primary">Sign In</a>
        </div>
    </div>
</body>
</html>
