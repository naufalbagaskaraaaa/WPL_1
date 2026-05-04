@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Praktikum 1: Barcode Scanner</h5>
                </div>
                <div class="card-body text-center">
                    <!-- Area Scanner -->
                    <div id="reader" style="width: 100%; min-height: 300px;"></div>
                    
                    <div class="mt-3">
                        <button id="btn-start" class="btn btn-success">Mulai Scan</button>
                        <button id="btn-stop" class="btn btn-danger" style="display: none;">Stop Scan</button>
                    </div>
                </div>
            </div>
            
            <!-- Hasil Scan -->
            <div class="card shadow-sm" id="card-hasil" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Hasil Scan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID Barang</th>
                            <td id="res-id">-</td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td id="res-nama">-</td>
                        </tr>
                        <tr>
                            <th>Harga</th>
                            <td id="res-harga">-</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-page')
<!-- Library HTML5 QR Code -->
<script src="https://unpkg.com/html5-qrcode"></script>

<!-- SweetAlert2 untuk Notifikasi -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Audio Beep
    const beepSound = new Audio('/sounds/beep.mp3');

    // Variabel Scanner
    let html5QrCode;

    // DOM Elements
    const btnStart = document.getElementById('btn-start');
    const btnStop = document.getElementById('btn-stop');
    const cardHasil = document.getElementById('card-hasil');
    const readerDiv = document.getElementById('reader');

    // Initialize HTML5 QR Code Scanner
    document.addEventListener("DOMContentLoaded", function() {
        html5QrCode = new Html5Qrcode("reader");

        // Event Listener Tombol Mulai
        btnStart.addEventListener('click', () => {
            startScanner();
        });

        // Event Listener Tombol Stop
        btnStop.addEventListener('click', () => {
            stopScanner();
        });
    });

    function startScanner() {
        // Kosongkan hasil sebelumnya
        cardHasil.style.display = 'none';
        document.getElementById('res-id').innerHTML = '-';
        document.getElementById('res-nama').innerHTML = '-';
        document.getElementById('res-harga').innerHTML = '-';

        // Konfigurasi Scanner
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        // Mulai kamera (Environment = kamera belakang)
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
                readerDiv.innerHTML = ''; // bersihkan div reader
            }).catch(err => {
                console.error("Gagal menghentikan scanner", err);
            });
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        stopScanner();
        beepSound.play();
        cariBarang(decodedText);
    }

    function onScanFailure(error) {
        console.error("Scan failed:", error);
    }

    // Fungsi AJAX mencari barang
    function cariBarang(id_barang) {
        // Tampilkan loading SweetAlert
        Swal.fire({
            title: 'Mencari Data...',
            text: 'Sedang mengecek ID: ' + id_barang,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Request menggunakan Fetch API / Axios (kita pakai Fetch API native)
        fetch(`/barcode/cari/${id_barang}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Tutup Swal Loading
                    Swal.close();

                    // Tampilkan Hasil di Tabel
                    cardHasil.style.display = 'block';
                    document.getElementById('res-id').innerHTML = data.data.id_barang;
                    document.getElementById('res-nama').innerHTML = data.data.nama;
                    
                    // Format Harga Rupiah
                    let formatHarga = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.data.harga);
                    document.getElementById('res-harga').innerHTML = formatHarga;

                    // SweetAlert Notif Success Singkat
                    Swal.fire({
                        icon: 'success',
                        title: 'Ditemukan!',
                        text: data.data.nama,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    // Tampilkan Error 404
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ditemukan',
                        text: `Barang dengan barcode ${id_barang} tidak terdaftar!`,
                    });
                }
            })
            .catch(error => {
                console.error("Error AJAX:", error);
                Swal.fire('Error', 'Terjadi kesalahan sistem/jaringan!', 'error');
            });
    }
</script>
@endsection
