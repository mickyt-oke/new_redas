<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Session Expired | NIS-REDAS</title>
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
        .card i { font-size: 2.4rem; color: #b45309; }
        h1 { font-size: 1.25rem; margin: 16px 0 8px; }
        p { font-size: .92rem; color: #4b5563; line-height: 1.5; }
        .note {
            margin-top: 16px;
            padding: 10px 14px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            font-size: .82rem;
            color: #065f46;
        }
        .actions { margin-top: 24px; display: flex; gap: 12px; justify-content: center; }
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
        <i class="fas fa-clock"></i>
        <h1>Your session has expired</h1>
        <p>For your security, this session timed out due to inactivity. Please log in again to continue.</p>
        <div class="note">If you were filling out a return form, your draft was saved automatically in this browser and will reload once you reopen the form.</div>
        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            <a href="{{ route('login') }}" class="btn btn-primary">Log In Again</a>
        </div>
    </div>
</body>
</html>
