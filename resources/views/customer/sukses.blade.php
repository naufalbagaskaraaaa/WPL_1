<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 root-style text-center p-4">
                <div class="card-body">
                    <h3 class="text-success fw-bold mb-3">Pembayaran Berhasil!</h3>
                    <p class="text-muted mb-4">Terima kasih <strong>{{ $pesanan->nama_customer }}</strong>, pesanan Anda dengan ID <strong>{{ $pesanan->transaction_id }}</strong> telah lunas dan sedang kami proses.</p>

                    <div class="p-3 bg-white border border-2 border-secondary rounded d-inline-block shadow-sm">
                        <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code Transaksi" class="img-fluid" style="width: 250px; height: 250px;">
                    </div>
                    <div class="mt-2 text-muted fw-bold">Scan QR untuk Verifikasi Transaksi</div>

                    <hr class="my-4">
                    <div class="text-start">
                        <h6 class="fw-bold">Rincian Pembelian:</h6>
                        <ul class="list-group mb-3">
                            @foreach($pesanan->detailPesanan as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $item->barang->nama }} (x{{ $item->jumlah }})
                                    <span>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                            <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-success fw-bold">
                                TOTAL BAYAR
                                <span>Rp{{ number_format($pesanan->total, 0, ',', '.') }}</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('customer.index') }}" class="btn btn-primary w-100 fw-bold py-2 mt-2">Kembali ke Halaman Utama</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
