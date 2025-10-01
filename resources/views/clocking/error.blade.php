
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clocking Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        }
        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .error-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="card error-card p-4 text-center" style="max-width: 400px; width: 90%;">
            <div class="error-icon">
                <i class="bi bi-exclamation-triangle text-white" style="font-size: 2rem;"></i>
            </div>
            
            <h2 class="text-danger mb-3">Access Denied</h2>
            
            <div class="alert alert-danger">
                {{ $message }}
            </div>
            
            @if(isset($ip_address))
                <small class="text-muted">
                    <i class="bi bi-wifi"></i> IP Address: {{ $ip_address }}
                </small>
            @endif
            
            @if(isset($email))
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="bi bi-envelope"></i> Email: {{ $email }}
                    </small>
                </div>
            @endif
            
            @if(isset($error) && app()->environment('local'))
                <details class="mt-3">
                    <summary class="text-muted">Technical Details</summary>
                    <small class="text-muted">{{ $error }}</small>
                </details>
            @endif
            
            <hr>
            <small class="text-muted">
                Please contact your administrator if this issue persists.
            </small>
        </div>
    </div>
</body>
</html>