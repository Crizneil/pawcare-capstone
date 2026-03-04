<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - PawCare</title>
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
        <img src="{{ asset('assets/images/paws-8.png') }}" alt="" class="paw" />
        <img src="{{ asset('assets/images/paws-9.png') }}" alt="" class="paw" />
        <img src="{{ asset('assets/images/paws-10.png') }}" alt="" class="paw" />
        <img src="{{ asset('assets/images/paws-11.png') }}" alt="" class="paw" />
    </div>

    <div class="wrapper">
        <div class="card">
            <div class="login-view">
                <header class="logo-wrap">
                    <img src="{{ asset('assets/images/newlogo.png') }}" alt="PawCare" />
                </header>
                <h1 class="title-main mt-2 mb-3">RESET PASSWORD</h1>
                <p class="subtitle text-center mb-4">Enter your email address and we will send you a link to securely
                    reset your password.</p>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-4 small mb-4"
                        style="background:#eaf8ed; color:#2d8a4e;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 small mb-4"
                        style="background:#fdf2f2; color:#d93025;">
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="field floating mb-4">
                        <input type="email" id="email" name="email" placeholder=" " value="{{ old('email') }}" required
                            autofocus />
                        <label for="email">Email Address</label>
                    </div>

                    <div class="submit-row">
                        <button type="submit" class="btn-primary" style="background: #f1aa6f; color: #2c3e50;">
                            <span class="fw-bold">Send Password Reset Link</span>
                            <span class="paw-emoji">📧</span>
                        </button>
                    </div>

                    <p class="cta-text-center pt-4" style="border-top: 1px solid #eee;">
                        Remembered your password? <a href="{{ route('login') }}" class="link"><strong>Back to
                                login</strong></a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
