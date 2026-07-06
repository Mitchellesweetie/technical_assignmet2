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

   <link rel="stylesheet" href="{{ asset('css/login.css') }}">
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
