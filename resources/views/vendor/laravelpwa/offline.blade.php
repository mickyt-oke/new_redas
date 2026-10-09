<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline | NIS-REDAS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            color: #0f172a;
            text-align: center;
            padding: 24px;
        }
        .offline-card {
            max-width: 420px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            padding: 40px 32px;
        }
        .offline-card img {
            width: 64px;
            height: 64px;
            margin-bottom: 16px;
        }
        .offline-card h1 {
            font-size: 1.4rem;
            margin: 0 0 10px;
        }
        .offline-card p {
            font-size: 0.95rem;
            color: #475569;
            margin: 0 0 24px;
            line-height: 1.5;
        }
        .offline-card button {
            background: #003d1a;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
        }
        .offline-card button:hover { background: #005a27; }
    </style>
</head>
<body>
    <div class="offline-card">
        <img src="{{ asset('assets/images/nis.png') }}" alt="NIS">
        <h1>You are offline</h1>
        <p>Please check your internet connection and try again. NIS-REDAS requires a network connection to authenticate and submit returns.</p>
        <button onclick="window.location.reload()">Retry</button>
    </div>
</body>
</html>
