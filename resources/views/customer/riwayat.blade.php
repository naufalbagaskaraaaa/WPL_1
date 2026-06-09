<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pesanan Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container">
        <a href="{{ route('customer.index') }}" class="btn btn-secondary mb-3">&laquo; Kembali ke Pemesanan</a>
        <h2>Riwayat Pesanan Lunas (Semua Customer)</h2>
        <hr>

        <div class="row">
            @forelse($pesanans as $p)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm text-center">
                    <div class="card-header bg-success text-white">
                        <strong>{{ $p->transaction_id }}</strong>
                    </div>
                    <div class="card-body">
                        <img src="data:image/png;base64,{{ $p->qrBase64 }}" alt="QR Code" class="img-fluid mb-3" style="max-width: 150px;">
                        <h5 class="card-title">{{ $p->nama_customer }}</h5>
                        <p class="card-text text-muted">Total: Rp {{ number_format($p->total, 0, ',', '.') }}</p>
                        <small class="text-secondary">{{ $p->created_at->format('d M Y H:i') }}</small>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">
                <p>Belum ada riwayat pesanan (Lunas) di dalam database.</p>
            </div>
            @endforelse
        </div>
    </div>
</body>
</html>