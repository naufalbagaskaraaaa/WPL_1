<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .otp-field {
            width: 100%;
            text-align: center;
            letter-spacing: 10px;
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="card-body p-4 text-center">
                        <h4>Masukkan Kode OTP</h4>
                        <p class="text-muted small">Cek email Anda untuk kode 6 karakter</p>

                        @if(session('error'))
                        <div class="alert alert-danger small">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('otp.process') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <input type="text" name="otp" class="form-control otp-field" maxlength="6" required autofocus>
                            </div>
                            @error('otp')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                            @enderror
                            <button type="submit" class="btn btn-primary w-100">Verifikasi & Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>