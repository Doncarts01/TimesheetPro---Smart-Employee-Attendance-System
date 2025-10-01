<!doctype html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock Scan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            text-align: center;
            background: #f5f5f5;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        #status {
            font-size: 16px;
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background: #e3f2fd;
        }
        .error {
            background: #ffebee !important;
            color: #c62828;
        }
        .success {
            background: #e8f5e9 !important;
            color: #2e7d32;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .retry-btn {
            margin-top: 15px;
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .retry-btn:hover {
            background: #1976D2;
        }
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Employee Clocking System</h2>
        <div id="spinner" class="spinner"></div>
        <p id="status">Fetching your precise location...</p>
        <button id="retryBtn" class="retry-btn hidden" onclick="getLocation()">Retry Location</button>
    </div>

    <script>
        const token = "{{ $token }}";
        const statusEl = document.getElementById('status');
        const spinnerEl = document.getElementById('spinner');
        const retryBtn = document.getElementById('retryBtn');
        let validateUrl = "{{ route('clock.validate') }}";
        
        // Force HTTPS if page is loaded over HTTPS
        if (window.location.protocol === 'https:') {
            validateUrl = validateUrl.replace('http://', 'https://');
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let attemptCount = 0;
        const maxAttempts = 2;

        function showError(message) {
            statusEl.textContent = message;
            statusEl.className = "error";
            spinnerEl.classList.add('hidden');
            retryBtn.classList.remove('hidden');
        }

        function showSuccess(message) {
            statusEl.textContent = message;
            statusEl.className = "success";
            spinnerEl.classList.add('hidden');
        }

        function showLoading(message) {
            statusEl.textContent = message;
            statusEl.className = "";
            spinnerEl.classList.remove('hidden');
            retryBtn.classList.add('hidden');
        }

        function validateLocation(position) {
            showLoading("Location acquired. Validating...");
            
            const payload = {
                token: token,
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy
            };

            console.log('Sending payload:', payload);
            console.log('Accuracy:', position.coords.accuracy + 'm');

            fetch(validateUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Server error');
                        });
                    }
                    
                    return response.json();
                })
                .then(json => {
                    console.log('Response data:', json);
                    
                    if (json.next === "oauth") {
                        showSuccess(json.message);
                        
                        // Redirect to Google OAuth
                        setTimeout(() => {
                            window.location.href = json.redirect;
                        }, 1000);
                    } else {
                        showError(json.message);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showError("Error: " + error.message);
                });
        }

        function success(position) {
            console.log('Location acquired:', {
                lat: position.coords.latitude,
                lon: position.coords.longitude,
                accuracy: position.coords.accuracy
            });
            
            validateLocation(position);
        }

        function error(err) {
            console.error('Geolocation error:', err);
            attemptCount++;
            
            let message = "Location error: ";
            
            switch(err.code) {
                case err.PERMISSION_DENIED:
                    message = "Please allow location access to continue. Check your browser settings.";
                    break;
                case err.POSITION_UNAVAILABLE:
                    message = "Location information unavailable. Please ensure GPS is enabled.";
                    break;
                case err.TIMEOUT:
                    if (attemptCount < maxAttempts) {
                        showLoading("Location request timed out. Retrying with lower accuracy...");
                        // Retry with lower accuracy requirements
                        setTimeout(() => {
                            navigator.geolocation.getCurrentPosition(success, error, {
                                enableHighAccuracy: false,
                                timeout: 15000,
                                maximumAge: 5000
                            });
                        }, 1000);
                        return;
                    } else {
                        message = "Location request timed out after " + maxAttempts + " attempts. Please try again or move to an area with better GPS signal.";
                    }
                    break;
                default:
                    message += err.message;
            }
            
            showError(message);
        }

        function getLocation() {
            attemptCount = 0;
            
            // Check if geolocation is supported
            if (!navigator.geolocation) {
                showError("Geolocation is not supported by your browser.");
                return;
            }

            showLoading("Fetching your precise location...");
            
            // First attempt with high accuracy
            navigator.geolocation.getCurrentPosition(success, error, {
                enableHighAccuracy: true,
                timeout: 20000, // Increased to 20 seconds
                maximumAge: 0
            });
        }

        // Auto-start location fetch on page load
        getLocation();
    </script>

</body>

</html>