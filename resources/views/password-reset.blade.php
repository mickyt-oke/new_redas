<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset password</title>
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body style="margin:0; padding:0;">
<div style="max-width:520px; margin: 40px auto; padding: 20px;">
    <h2>Reset password</h2>

    @if ($errors->any())
        <div style="background:#ffecec; border:1px solid #ffb3b3; padding:10px; margin-bottom:15px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.reset', ['token' => $token]) }}">
        @csrf
        <div style="margin-bottom:12px;">
            <label>New password</label><br/>
            <input type="password" name="password" required style="width:100%; padding:10px;" />
        </div>

        <div style="margin-bottom:12px;">
            <label>Confirm new password</label><br/>
            <input type="password" name="password_confirmation" required style="width:100%; padding:10px;" />
        </div>

        <button type="submit" style="padding:10px 16px; cursor:pointer;">Reset password</button>
    </form>
</div>
</body>
</html>
