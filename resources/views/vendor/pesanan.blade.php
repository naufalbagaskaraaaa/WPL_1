<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pesanan Lunas - {{ $vendor->nama_vendor }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container">
        <a href="{{ route('vendor.index') }}" class="btn btn-secondary mb-3">&laquo; Kembali</a>
        <h2>Pesanan Lunas - {{ $vendor->nama_vendor }}</h2>
        <hr>

        <div class="table-responsive">
            <table class="table table-bordered table-striped shadow-sm bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Waktu (Timestamp)</th>
                        <th>Customer</th>
                        <th style="width: 40%">Pesanan (Hanya milik Anda)</th>
                        <th>Grand Total Pesanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $p)
                    <tr>
                        <td>{{ $p->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $p->nama_customer }}</td>
                        <td>
                            <ul>
                                @foreach($p->detailPesanan as $item)
                                    <li>
                                        <strong>{{ $item->menu->nama_menu }}</strong> 
                                        ({{ $item->jumlah }}x) - 
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        <br>
                                        <small class="text-muted">Catatan: {{ $item->catatan ?? '-' }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada pesanan lunas saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>