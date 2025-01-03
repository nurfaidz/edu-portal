<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-light text-dark vh-100 d-flex justify-content-center align-items-center">
<div class="card text-center shadow-lg" style="max-width: 400px;">
    <div class="card-body">
        <h5 class="card-title">QR Code</h5>
        <p class="card-text text-muted">Scan QR Code di bawah ini</p>
        <div class="p-3 bg-white border rounded">
            <img src="data:image/png;base64,{{ $barcode }}" alt="QR Code" class="img-fluid">
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
