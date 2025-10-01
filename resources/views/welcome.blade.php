<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock QR</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            max-width: 420px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            text-align: center;
            background: #fff;
        }

        .card h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #325b06;
        }

        .card p {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: #555;
        }

        .qr-code {
            margin: 1.5rem 0;
        }

        .qr-code img {
            border: 6px solid #f1f1f1;
            border-radius: 12px;
        }

        .btn-custom {
            background-color: #325b06;
            color: white;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            border-radius: 12px;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn-custom:hover {
            background-color: #243e07;
            text-decoration: none;
        }

        .alert {
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>Clock In / Out</h1>
        <p>Scan with your phone to clock in/out securely.</p>

        <div class="qr-code">
            <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode(route('clock.scan', ['token' => $token])) }}"
                alt="Clocking QR Code" />
        </div>

        <p class="mb-3">If you’re on your phone:</p>
        <a href="{{ route('clock.scan', ['token' => $token]) }}" class="btn-custom">Tap to Clock</a>

        @if (session('error'))
            <div class="alert alert-danger mt-3">
                {{ session('error') }}
            </div>
        @endif
    </div>
</body>

</html>
