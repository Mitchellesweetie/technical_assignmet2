<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Site Management') }} - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        :root {
            --bg-start: #0f172a;
            --bg-end: #1e293b;
            --card-bg: rgba(255, 255, 255, 0.96);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --field-border: #cbd5e1;
            --field-focus: rgba(37, 99, 235, 0.25);
            --danger: #dc2626;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Inter", sans-serif;
            color: var(--text-main);
            /* background: radial-gradient(circle at top left, #334155 0%, var(--bg-start) 45%, var(--bg-end) 100%); */
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--card-bg);
            border-radius: 18px;
            padding: 32px 28px;
            box-shadow: 0 24px 50px rgba(2, 6, 23, 0.35);
            backdrop-filter: blur(8px);
        }

        .title {
            margin: 0 0 8px;
            font-size: 1.7rem;
            font-weight: 700;
            text-align: center;
        }

        .subtitle {
            margin: 0 0 28px;
            color: var(--text-muted);
            text-align: center;
            font-size: 0.95rem;
        }

        .field-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.92rem;
            font-weight: 600;
        }

        .input {
            width: 100%;
            border: 1px solid var(--field-border);
            border-radius: 12px;
            padding: 13px 14px;
            font-size: 0.98rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #fff;
        }

        .input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--field-focus);
        }

        .input.is-invalid {
            border-color: var(--danger);
        }

        .error-text {
            margin-top: 7px;
            color: var(--danger);
            font-size: 0.84rem;
            font-weight: 500;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
        }

        .forgot-link {
            color: var(--primary);
            font-size: 0.9rem;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 13px 18px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(37, 99, 235, 0.28);
        }

        .btn:active {
            transform: translateY(0);
        }

        .otp-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, 0.55);
            z-index: 999;
            padding: 16px;
        }

        .otp-modal.show {
            display: flex;
        }

        .otp-modal-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 24px 50px rgba(2, 6, 23, 0.35);
        }

        .otp-title {
            margin: 0 0 8px;
            font-size: 1.15rem;
            font-weight: 700;
        }

        .otp-subtitle {
            margin: 0 0 16px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .btn-secondary {
            width: 100%;
            border: 1px solid var(--field-border);
            border-radius: 12px;
            padding: 13px 18px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            background: #fff;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <main class="login-card">
        <h1 class="title">Technical Assignment</h1>
        <p class="subtitle">Sign in to continue to your dashboard.</p>

        <form method="POST" action="{{ route('login.submit') }}">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @csrf

            <div class="field-group">
                <label for="email">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username" class="input @error('email') is-invalid @enderror"
                    placeholder="you@example.com">
                {{-- @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror --}}
            </div>

            <div class="field-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="input @error('password') is-invalid @enderror" placeholder="Enter your password">
                {{-- @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror --}}
            </div>

            <div class="form-row" style="display:none">
                <label class="remember" for="remember_me">
                    <input id="remember_me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>

                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn" id="colorBtn">Log in</button>
            

            <div class="alert alert-primary mt-3" role="alert">
                email:admin@gmail.com <br>
                password:Admin@123!
            </div>
        </form>
    </main>

    {{-- <div id="otpModal" class="otp-modal {{ session('show_otp_modal') || $errors->has('code') ? 'show' : '' }}">
        <div class="otp-modal-card">
            <h2 class="otp-title">Enter verification code</h2>
            <p class="otp-subtitle">Use the 6-digit code sent to your email.</p>

            <form method="POST" action="{{ route('login.verify.code') }}">
                @csrf
                <div class="field-group">
                    <label for="code">Verification Code</label>
                    <input id="code" type="text" name="code" maxlength="6" required class="input @error('code') is-invalid @enderror"
                        placeholder="123456" inputmode="numeric" pattern="[0-9]{6}">
                    @error('code')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn" >Verify & Continue</button>
                <button type="button" id="closeOtpModal" class="btn-secondary">Cancel</button>
            </form>
        </div>
    </div> --}}

    {{-- <script>
        (function () {
            const modal = document.getElementById('otpModal');
            const closeBtn = document.getElementById('closeOtpModal');
            if (!modal || !closeBtn) return;
            closeBtn.addEventListener('click', function () {
                modal.classList.remove('show');
            });
        })();
    </script> --}}
    <script>
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('colorBtn');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;

            submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            ⏳ Authenticating...
        `;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
    </script>
</body>

</html>
