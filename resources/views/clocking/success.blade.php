
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clocking {{ ucfirst($action) }} Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .success-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .success-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .time-display {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        .action-badge {
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 25px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="card success-card p-4 text-center" style="max-width: 400px; width: 90%;">
            <div class="success-icon">
                <i class="bi bi-check-lg text-white" style="font-size: 2rem;"></i>
            </div>
            
            <h2 class="text-success mb-3">{{ $message }}</h2>
            
            <div class="mb-3">
                @if($action === 'clock_in')
                    <span class="badge bg-success action-badge">
                        <i class="bi bi-clock"></i> CLOCKED IN
                    </span>
                @else
                    <span class="badge bg-primary action-badge">
                        <i class="bi bi-clock-history"></i> CLOCKED OUT
                    </span>
                @endif
            </div>
            
            <div class="mb-3">
                <strong>Employee:</strong> {{ $employee->name }}<br>
                <small class="text-muted">{{ $employee->email }}</small>
            </div>
            
            @if($action === 'clock_in')
                <div class="time-display text-success">
                    Clock In: {{ \Carbon\Carbon::parse($clock_in_time)->format('h:i A') }}
                </div>
                <div class="text-muted">
                    {{ \Carbon\Carbon::parse($clock_in_time)->format('F j, Y') }}
                </div>
            @else
                <div class="time-display text-primary">
                    Clock Out: {{ \Carbon\Carbon::parse($clock_out_time)->format('h:i A') }}
                </div>
                <div class="text-muted mb-2">
                    {{ \Carbon\Carbon::parse($clock_out_time)->format('F j, Y') }}
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-stopwatch"></i>
                    <strong>Work Duration:</strong> {{ $work_duration }}
                </div>
            @endif
            
            <hr>
            <small class="text-muted">
                <i class="bi bi-shield-check"></i> 
                Authenticated via Google OAuth
            </small>
        </div>
    </div>
</body>
</html>

