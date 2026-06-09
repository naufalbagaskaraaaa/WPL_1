<!DOCTYPE html>
<html lang="id">
<head>
    <title>Scan Pesanan - {{ $vendor->nama_vendor }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light p-5">
    <div class="container">
        <a href="{{ route('vendor.index') }}" class="btn btn-secondary mb-3">&laquo; Kembali</a>
        <h2>Scan Pesanan (QR) - {{ $vendor->nama_vendor }}</h2>
        <hr>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Arahkan Kamera ke QR Code Customer</h5>
                    </div>
                    <div class="card-body text-center">
                        <div id="reader" style="width: 100%; min-height: 300px;"></div>
                        <div class="mt-3">
                            <button id="btn-start" class="btn btn-success">Mulai Scan</button>
                            <button id="btn-stop" class="btn btn-danger" style="display: none;">Stop Scan</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Hasil Scan -->
                <div class="card shadow-sm" id="card-hasil" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Hasil Scan (Validasi)</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">ID Pesanan</th>
                                <td id="res-id">-</td>
                            </tr>
                            <tr>
                                <th>Customer</th>
                                <td id="res-nama">-</td>
                            </tr>
                            <tr>
                                <th>Status Pembayaran</th>
                                <td id="res-status" class="fw-bold">-</td>
                            </tr>
                            <tr>
                                <th>Item Pesanan (Toko Anda Saja)</th>
                                <td id="res-items">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const beepSound = new Audio('/sounds/beep.mp3');
        let html5QrCode;

        const btnStart = document.getElementById('btn-start');
        const btnStop = document.getElementById('btn-stop');
        const cardHasil = document.getElementById('card-hasil');
        const readerDiv = document.getElementById('reader');

        document.addEventListener("DOMContentLoaded", function() {
            html5QrCode = new Html5Qrcode("reader");

            btnStart.addEventListener('click', () => {
                startScanner();
            });

            btnStop.addEventListener('click', () => {
                stopScanner();
            });
        });

        function startScanner() {
            cardHasil.style.display = 'none';
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
                .then(() => {
                    btnStart.style.display = 'none';
                    btnStop.style.display = 'inline-block';
                })
                .catch(err => {
                    console.error("Gagal memulai kamera", err);
                    Swal.fire('Error', 'Tidak dapat mengakses kamera. Pastikan izin diberikan.', 'error');
                });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    btnStart.style.display = 'inline-block';
                    btnStop.style.display = 'none';
                    readerDiv.innerHTML = ''; 
                }).catch(err => {
                    console.error("Gagal menghentikan scanner", err);
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            stopScanner();
            beepSound.play();
            cekPesanan(decodedText);
        }

        function onScanFailure(error) {
            // Abaikan peringatan failure per-frame
        }

        function cekPesanan(qr_data) {
            Swal.fire({
                title: 'Mengecek Pesanan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`{{ route('vendor.scan.check', $vendor->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ qr_data: qr_data })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Berhasil', 'Pesanan ditemukan!', 'success');
                    
                    document.getElementById('res-id').innerHTML = data.data.order_id;
                    document.getElementById('res-nama').innerHTML = data.data.customer;
                    document.getElementById('res-status').innerHTML = data.data.status_bayar;
                    
                    if(data.data.status_bayar === 'LUNAS') {
                        document.getElementById('res-status').className = 'text-success fw-bold';
                    } else {
                        document.getElementById('res-status').className = 'text-danger fw-bold';
                    }

                    // Render daftar item
                    let ul = '<ul class="mb-0 ps-3">';
                    data.data.items.forEach(item => {
                        ul += `<li>${item}</li>`;
                    });
                    ul += '</ul>';
                    document.getElementById('res-items').innerHTML = ul;

                    cardHasil.style.display = 'block';
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            });
        }
    </script>
</body>
</html>