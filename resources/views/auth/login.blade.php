<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TimesheetPro - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <!-- Bootstrap css -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <!-- icons -->
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #235436, #101c02);
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .login-card .logo img {
            height: 60px;
            margin-bottom: 1.5rem;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #2c304d;
        }

        .form-subtitle {
            color: #6c757d;
            margin-bottom: 2rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.65rem 1rem;
        }

        .form-control:focus {
            border-color: #101c02;
            box-shadow: 0 0 0 0.2rem rgba(91, 115, 232, 0.25);
        }

        .btn-primary {
            background-color: #101c02;
            border-color: #101c02;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
        }

        .btn-primary:hover {
            background-color: #325b06;
            border-color: #325b06;
        }

        .form-check-label {
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('backend/assets/images/opendoors.png') }}" alt="TimesheetPro">
        </div>

        <h1 class="form-title">Welcome Back</h1>
        <p class="form-subtitle">Sign in to your Timesheet Management System</p>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3 text-start">
                <label for="emailaddress" class="form-label">Email | Phone | Username</label>
                <input class="form-control @error('login') is-invalid @enderror" type="text" id="emailaddress"
                    required placeholder="Enter your email" name="login">
                @error('login')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <div class="input-group input-group-merge">
                    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter your password" name="password">
                    <div class="input-group-text" data-password="false">
                        <span class="password-eye"></span>
                    </div>
                </div>
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember_me" class="form-check-input">
                    <label class="form-check-label" for="remember_me">Remember me</label>
                </div>
            </div>

            <div class="d-grid">
                <button class="btn btn-primary" type="submit">Log In</button>
            </div>
        </form>
    </div>

    <!-- Vendor js -->
    <script src="{{ asset('backend/assets/js/vendor.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('backend/assets/js/app.min.js') }}"></script>
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        @if (Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}";
            toastr[type]("{{ Session::get('message') }}");
        @endif
    </script>
</body>

</html>
