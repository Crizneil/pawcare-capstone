<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="author" content="wpOceans" />
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/newicon.png') }}" />
  <link href="{{ asset('assets/css/themify-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/flaticon.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/owl.carousel.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/owl.theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/slick.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/slick-theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/swiper.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/owl.transitions.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/jquery.fancybox.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/odometer-theme-default.css') }}" rel="stylesheet" />
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
    <a href="{{ route('home') }}" class="back-home-btn">
      <i class="ti-arrow-left"></i> Back to Home
    </a>
    <div class="card">
      <div class="login-view" id="emailView">
        <header class="logo-wrap">
          <img src="{{ asset('assets/images/newlogo.png') }}" alt="PawCare" />
        </header>
        <h1 class="title-main">LOG IN</h1>
        <p class="subtitle">Sign in to continue cuddles, walks, and wagging tails.</p>

        <form id="loginForm" action="{{ route('admin.login.post') }}" method="POST">
          @csrf
          <div class="field floating">
            <input type="text" id="email" name="email" placeholder=" " value="{{ old('email') }}" required />
            <label for="email">Email or phone</label>
          </div>
          @error('email')
            <div class="status error" style="margin-top: 10px;">{{ $message }}</div>
          @enderror

          <div class="field floating">
            <input type="password" id="password" name="password" placeholder=" " required />
            <label for="password">Password</label>
          </div>
          @error('password')
            <div class="status error" style="margin-top: 10px;">{{ $message }}</div>
          @enderror

          <div class="extras">
            <label class="show-password">
              <input type="checkbox" id="showPassword" />
              <span>Show password</span>
            </label>
            <a class="link">Forgot password?</a>
          </div>

          <div class="submit-row">
            <button type="submit" class="btn-primary">
              <span>Log in</span>
              <span class="paw-emoji">🐾</span>
            </button>
          </div>

          <p class="cta-text-center">
            New here? <strong>Join the pack</strong> and track.
          </p>

          <div class="other-login">
            <button type="button" id="otherWaysBtn">Other ways to login</button>
          </div>

          <div id="status" class="status">
            @if(session('error'))
              <div class="status error">{{ session('error') }}</div>
            @endif
            @if(session('success'))
              <div class="status success">{{ session('success') }}</div>
            @endif
          </div>

          <div class="bottom-note">
            Protected with extra treats and belly rubs.
          </div>
        </form>
      </div>

      <div class="phone-view" id="phoneView">
        <header class="logo-wrap">
          <img src="{{ asset('assets/images/newlogo.png') }}" alt="PawCare" />
        </header>

        <h1 class="title-main">LOG IN</h1>
        <p class="subtitle">Use your phone number and a secure code.</p>
        <p class="phone-note">We'll send a one-time authentication code to your phone.</p>

        <form id="phoneLoginForm">
          <div class="field">
            <label for="phone">Phone number</label>
            <input type="tel" id="phone" placeholder="+639*********" required />
          </div>

          <div class="field">
            <label for="code">Authentication code</label>
            <input type="text" id="code" placeholder="Enter 6-digit code" required />
          </div>

          <div class="submit-row">
            <button type="submit" class="btn-primary">
              <span>Log in with phone</span>
            </button>
          </div>

          <div class="switch-back">
            <button type="button" id="backToEmailBtn">Back to email login</button>
          </div>

          <div id="statusPhone" class="status"></div>

          <div class="bottom-note">
            Protected with extra treats and belly rubs.
          </div>
        </form>
      </div>

      <div class="logged-view">
        <h2>Welcome back!</h2>
        <p>
          Your dashboard is getting the toys ready. Enjoy the full, glowing view
          while we fetch everything for you.
        </p>
        <div class="tag"></div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <script>
    const loginForm = document.getElementById("loginForm");
    const phoneLoginForm = document.getElementById("phoneLoginForm");
    const statusEl = document.getElementById("status");
    const statusPhoneEl = document.getElementById("statusPhone");
    const passwordInput = document.getElementById("password");
    const emailView = document.getElementById("emailView");
    const phoneView = document.getElementById("phoneView");
    const otherWaysBtn = document.getElementById("otherWaysBtn");
    const backToEmailBtn = document.getElementById("backToEmailBtn");
    const showPasswordCheckbox = document.getElementById("showPassword");

    showPasswordCheckbox.addEventListener("change", () => {
      passwordInput.type = showPasswordCheckbox.checked ? "text" : "password";
    });

    otherWaysBtn.addEventListener("click", () => {
      emailView.style.display = "none";
      phoneView.style.display = "block";
      statusEl.textContent = "";
    });

    backToEmailBtn.addEventListener("click", () => {
      phoneView.style.display = "none";
      emailView.style.display = "block";
      statusPhoneEl.textContent = "";
    });

    // Handle form submission - let it submit normally to Laravel
    loginForm.addEventListener("submit", function (e) {
      const email = document.getElementById("email").value.trim();
      const password = passwordInput.value.trim();

      if (!email || !password) {
        e.preventDefault();
        statusEl.innerHTML = '<div class="status error">Please fill in your email and password.</div>';
        return;
      }
      // Form will submit to Laravel route
    });

    phoneLoginForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const phone = document.getElementById("phone").value.trim();
      const code = document.getElementById("code").value.trim();

      if (!phone || !code) {
        statusPhoneEl.textContent = "Please fill in your phone number and code.";
        statusPhoneEl.className = "status error";
        return;
      }

      statusPhoneEl.textContent = "Verifying your code... please wait.";
      statusPhoneEl.className = "status";

      setTimeout(() => {
        document.body.classList.add("logged-in");
        statusPhoneEl.textContent = "You are in! Enjoy the full fluffy view.";
        statusPhoneEl.className = "status success";
      }, 800);
    });
  </script>
</body>

</html>