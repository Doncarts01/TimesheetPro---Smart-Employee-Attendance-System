<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }

        #qr-code-img {
            margin-top: 20px;
            border: 2px solid #dee2e6;
            padding: 10px;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <div class="container qr-container">
        <div class="card p-4 shadow-sm">
            <h1 class="mb-3">Clocking QR Code</h1>
            <p class="text-muted" id="status-message">Fetching your location to generate QR code...</p>

            <div id="qr-code-display">
                <img id="qr-code-img" src="https://via.placeholder.com/250" alt="QR Code Loading">
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const baseUrl = "{{ route('clock.validate') }}";
        const qrCodeImg = document.getElementById('qr-code-img');
        const statusMessage = document.getElementById('status-message');

        function success(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const accuracy = position.coords.accuracy; // in meters

            if (accuracy > 100) {
                statusMessage.textContent = `Low accuracy detected (${accuracy}m). Please move to open sky or enable GPS.`;
                statusMessage.classList.remove('text-muted');
                statusMessage.classList.add('text-warning');
                return;
            }

            const urlWithGeo = `${baseUrl}?latitude=${latitude}&longitude=${longitude}`;
            const qrApiUrl =
                `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(urlWithGeo)}`;

            qrCodeImg.src = qrApiUrl;
            statusMessage.textContent = `Scan this QR code to clock in/out. (Accuracy: ${Math.round(accuracy)}m)`;
            statusMessage.classList.remove('text-danger', 'text-warning');
            statusMessage.classList.add('text-muted');
        }

        function error(err) {
            let message = 'Error: Cannot get location. Please enable GPS.';
            if (err.code === 1) message = 'Geolocation access denied. Please allow location access.';
            if (err.code === 2) message = 'Position unavailable. Try again outdoors.';
            if (err.code === 3) message = 'Location request timed out. Refresh and try again.';

            statusMessage.textContent = message;
            statusMessage.classList.remove('text-muted');
            statusMessage.classList.add('text-danger');
            qrCodeImg.src = 'https://via.placeholder.com/250/f8d7da/212529?text=LOCATION+ERROR';
        }

        function generateQrCodeWithGeo() {
            if (!navigator.geolocation) {
                statusMessage.textContent = 'Geolocation is not supported by your browser.';
                statusMessage.classList.add('text-danger');
                qrCodeImg.src = 'https://via.placeholder.com/250/f8d7da/212529?text=BROWSER+ERROR';
                return;
            }

            navigator.geolocation.getCurrentPosition(success, error, {
                enableHighAccuracy: true,
                timeout: 10000, // allow more time for GPS
                maximumAge: 0 // don’t use cached location
            });
        }

        generateQrCodeWithGeo();
    </script>


</body>

</html>
