<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Service Unavailable | NIS-REDAS</title>
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
            background: #fffbeb;
            color: #92400e;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon svg { width: 32px; height: 32px; fill: currentColor; }
        .code {
            font-size: .85rem;
            font-weight: 700;
            color: #92400e;
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
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22.7 19l-9.1-15.6c-.4-.7-1.3-.7-1.7 0L2.8 19c-.3.5 0 1.2.6 1.2h18.7c.5 0 .9-.7.6-1.2zM12 17c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-4h-2V9h2v4z"/></svg>
        </div>
        <div class="code">Error 503</div>
        <h1>Service temporarily unavailable</h1>
        <p>NIS-REDAS is currently down for maintenance or experiencing a temporary outage. Please wait a few moments and try again.</p>
        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            <a href="{{ url('/') }}" class="btn btn-primary">Try Again</a>
        </div>
    </div>
</body>
</html>
