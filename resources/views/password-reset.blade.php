<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset password</title>
    @include('partials.head-meta')
    @laravelPWA
    <link rel="stylesheet" href="/assets/app.css">
    <style>
        .pw-wrap { position: relative; }
        .pw-wrap input { width: 100%; padding: 10px 58px 10px 14px; box-sizing: border-box; }
        .pw-toggle {
            position: absolute; right: 8px; top: 50%;
            transform: translateY(-50%); background: none; border: none;
            cursor: pointer; color: #006633; font-size: 0.8rem; font-weight: 600;
            padding: 4px 6px;
        }
        .pw-toggle:hover { color: #004d26; text-decoration: underline; }
        .pw-toggle .hide-text { display: none; }
        .pw-toggle.showing .show-text { display: none; }
        .pw-toggle.showing .hide-text { display: inline; }
    </style>
</head>
<body style="margin:0; padding:0; font-family: Inter, sans-serif;">

@include('partials.preloader')

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

    <form method="POST" action="{{ route('password.reset', ['token' => $token]) }}" class="needs-validation" novalidate>
        @csrf
        <div style="margin-bottom:12px;">
            <label>New password</label><br/>
            <div class="pw-wrap">
                <input type="password" id="password" name="password" required />
                <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                    <span class="show-text">Show</span>
                    <span class="hide-text">Hide</span>
                </button>
            </div>
        </div>

        <div style="margin-bottom:12px;">
            <label>Confirm new password</label><br/>
            <div class="pw-wrap">
                <input type="password" id="password_confirmation" name="password_confirmation" required />
                <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                    <span class="show-text">Show</span>
                    <span class="hide-text">Hide</span>
                </button>
            </div>
        </div>

        <button type="submit" style="padding:10px 16px; cursor:pointer;">Reset password</button>
    </form>
</div>

<script>
(function () {
    document.querySelectorAll('.pw-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.closest('.pw-wrap').querySelector('input');
            if (!input) return;
            var isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.classList.toggle('showing', !isText);
            btn.setAttribute('aria-pressed', String(!isText));
        });
    });
})();
</script>
</body>
</html>
