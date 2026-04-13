<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pilih Dasbor Vendor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container">
        <h2>Silakan Pilih Nama Vendor Anda</h2>
        <p class="text-muted">(Akses tanpa sistem login / Guest mode)</p>
        <div class="row mt-4">
            @foreach($vendors as $v)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $v->nama_vendor }}</h5>
                        <hr>
                        <a href="{{ route('vendor.menu', $v->id) }}" class="btn btn-primary btn-sm">Kelola Menu</a>
                        <a href="{{ route('vendor.pesanan', $v->id) }}" class="btn btn-success btn-sm">Lihat Pesanan</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>