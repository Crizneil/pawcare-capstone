<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Set New Password - PawCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/newicon.png') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/sass/style.css') }}" rel="stylesheet" />
</head>

<body class="pawcare-login">
    <div class="bg"></div>

    <div class="floating-paws">
        <img src="{{ asset('assets/images/paws-6.png') }}" alt="" class="paw" />
        <img src="{{ asset('assets/images/paws-7.png') }}" alt="" class="paw" />
    </div>

    <div class="wrapper">
        <div class="card" style="padding: 3rem 2rem;">
            <div class="login-view">
                <header class="logo-wrap mb-4">
                    <img src="{{ asset('assets/images/newlogo.png') }}" alt="PawCare" style="width: 140px;" />
                </header>
                <h2 class="title-main mb-3" style="font-size: 1.8rem; font-weight: 800; color:#2c3e50;">CHOOSE NEW
                    PASSWORD</h2>
                <p class="text-center text-muted small mb-4">You have successfully verified your email. Please choose a
                    strong new password for your account.</p>

                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 small mb-4"
                        style="background:#fdf2f2; color:#d93025; padding: 12px 20px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.update.action') }}" method="POST">
                    @csrf
                    <!-- Hidden Token for Security -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Disabled Email Field (readonly for security purposes but required by Laravel broker) -->
                    <div class="field floating mb-4">
                        <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required
                            readonly style="background: #f8f9fa; color: #6c757d; border-color: #e9ecef;" />
                        <label for="email">Account Email</label>
                    </div>

                    <div class="field floating mb-4">
                        <input type="password" id="password" name="password" placeholder=" " required autofocus />
                        <label for="password">New Password</label>
                    </div>

                    <div class="field floating mb-4">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder=" "
                            required />
                        <label for="password_confirmation">Confirm New Password</label>
                    </div>

                    <div class="form-check ms-1 mb-4 text-start">
                        <input class="form-check-input shadow-none cursor-pointer" type="checkbox" id="showPassword"
                            style="width: 16px; height: 16px;">
                        <label class="form-check-label text-muted small cursor-pointer user-select-none pt-1"
                            for="showPassword">
                            Show password
                        </label>
                    </div>

                    <div class="submit-row mt-4">
                        <button type="submit" class="btn-primary w-100 py-3"
                            style="background: #ff6b6b; border-radius: 50px; text-transform: uppercase;">
                            <span class="fw-bold">Update Password</span>
                            <span class="paw-emoji">🔒</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleCheckbox = document.getElementById('showPassword');
        const pass1 = document.getElementById('password');
        const pass2 = document.getElementById('password_confirmation');

        if (toggleCheckbox) {
            toggleCheckbox.addEventListener('change', function () {
                const type = this.checked ? 'text' : 'password';
                if (pass1) pass1.type = type;
                if (pass2) pass2.type = type;
            });
        }
    </script>
</body>

</html>
