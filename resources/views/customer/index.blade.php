<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemesanan Toko Buku (Customer)</title>
    <!-- CSS Framework Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Midtrans Snap Script -->
    <script src="{{ env('MIDTRANS_IS_PRODUCTION', false) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>

<body class="bg-light">

    <div class="container py-5">
        <h2 class="mb-4 text-center">Pemesanan Toko Buku</h2>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">1. Pilih Buku</h5>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Pilih Penerbit / Kategori (Vendor):</label>
                                <select id="pilihVendor" class="form-select">
                                    <option value="">-- Pilih Penerbit --</option>
                                    @foreach($vendors as $v)
                                    <option value="{{ $v->id }}">{{ $v->nama_vendor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Pilih Buku:</label>
                                <select id="pilihMenu" class="form-select" disabled>
                                    <option value="">-- Pilih Penerbit dulu --</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="btnTambahMenu" class="btn btn-primary w-100" disabled>
                                    + Tambahkan Buku
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Pesanan -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">2. Daftar Pesanan</h5>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover alignments-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="25%">Buku</th>
                                <th width="15%">Harga (Rp)</th>
                                <th width="15%">Jumlah</th>
                                <th width="20%">Catatan</th>
                                <th width="15%">Subtotal (Rp)</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelPesanan">
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center text-muted">Belum ada pesanan</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="4" class="text-end">Total Bayar:</td>
                                <td id="labelTotal">0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informasi Pembayaran -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">3. Proses Pembayaran</h5>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Metode Pembayaran (Sandbox):</label>
                        <select id="metodeBayar" class="form-select">
                            <option value="1">Virtual Account (Midtrans)</option>
                            <option value="2">QRIS (Midtrans)</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button id="btnBayar" class="btn btn-success w-100 fw-bold py-2">
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let keranjang = [];
        let stateMenu = [];

        // Setup Axios CSRF
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 1. Cascading Select (Vendor -> Menu)
        $('#pilihVendor').on('change', function() {
            let idvendor = $(this).val();
            let menuSelect = $('#pilihMenu');

            menuSelect.empty().append('<option value="">-- Menunggu data --</option>').prop('disabled', true);
            $('#btnTambahMenu').prop('disabled', true);

            if (!idvendor) {
                menuSelect.empty().append('<option value="">-- Pilih Vendor dulu --</option>');
                return;
            }

            axios.get(`/customer/menu/${idvendor}`)
                .then(res => {
                    if (res.data.status === 'success') {
                        stateMenu = res.data.data;
                        menuSelect.empty().append('<option value="">-- Pilih Menu --</option>');
                        if (stateMenu.length === 0) {
                            menuSelect.empty().append('<option value="">-- Buku Kosong --</option>');
                        } else {
                            stateMenu.forEach(item => {
                                menuSelect.append(`<option value="${item.id_barang}">${item.nama} - Rp${item.harga}</option>`);
                            });
                            menuSelect.prop('disabled', false);
                            $('#btnTambahMenu').prop('disabled', false);
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Gagal memuat menu', 'error');
                });
        });

        // 2. Tambah ke Tabel (POS Logik)
        $('#btnTambahMenu').click(function() {
            let idmenu = $('#pilihMenu').val();
            if (!idmenu) return Swal.fire('Peringatan', 'Pilih buku terlebih dahulu', 'warning');

            let menuData = stateMenu.find(m => m.id_barang == idmenu);
            let ext = keranjang.find(k => k.idmenu == idmenu);

            if (ext) {
                ext.jumlah += 1;
                ext.subtotal = ext.jumlah * ext.harga;
            } else {
                keranjang.push({
                    idmenu: menuData.id_barang,
                    nama_menu: menuData.nama,
                    harga: menuData.harga,
                    jumlah: 1,
                    subtotal: menuData.harga,
                    catatan: ''
                });
            }
            renderTabel();
        });

        // 3. Render Tabel & Total
        function renderTabel() {
            let html = '';
            let total = 0;

            if (keranjang.length === 0) {
                html = `<tr id="emptyRow"><td colspan="6" class="text-center text-muted">Belum ada pesanan</td></tr>`;
            } else {
                keranjang.forEach((item, index) => {
                    total += item.subtotal;
                    html += `
                    <tr>
                        <td>${item.nama_menu}</td>
                        <td>${item.harga.toLocaleString('id-ID')}</td>
                        <td>
                            <input type="number" min="1" class="form-control form-control-sm inp-jumlah" data-idx="${index}" value="${item.jumlah}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm inp-catatan" data-idx="${index}" value="${item.catatan}" placeholder="Contoh: pedas, tidak pakai es...">
                        </td>
                        <td>${item.subtotal.toLocaleString('id-ID')}</td>
                        <td>
                            <button class="btn btn-sm btn-danger btn-hapus" data-idx="${index}">X</button>
                        </td>
                    </tr>
                `;
                });
            }

            $('#tabelPesanan').html(html);
            $('#labelTotal').text(total.toLocaleString('id-ID'));
        }

        // Update jumlah item & catatan
        $(document).on('change', '.inp-jumlah', function() {
            let idx = $(this).data('idx');
            let val = parseInt($(this).val());
            if (val < 1) val = 1;
            keranjang[idx].jumlah = val;
            keranjang[idx].subtotal = val * keranjang[idx].harga;
            renderTabel();
        });

        $(document).on('input', '.inp-catatan', function() {
            let idx = $(this).data('idx');
            keranjang[idx].catatan = $(this).val();
        });

        $(document).on('click', '.btn-hapus', function() {
            let idx = $(this).data('idx');
            keranjang.splice(idx, 1);
            renderTabel();
        });

        // 4. Proses Simpan & Bayar (Midtrans Snap)
        $('#btnBayar').click(function() {
            if (keranjang.length === 0) return Swal.fire('Peringatan', 'Keranjang masih kosong', 'warning');

            let totalKeseluruhan = keranjang.reduce((sum, current) => sum + current.subtotal, 0);
            let dataPayload = {
                total: totalKeseluruhan,
                metode_bayar: $('#metodeBayar').val(),
                detail: keranjang
            };

            Swal.fire({
                title: 'Memproses...',
                text: 'Membuka gerbang pembayaran Midtrans',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            axios.post('/customer/simpan', dataPayload)
                .then(res => {
                    if (res.data.status === 'success') {
                        Swal.close();
                        let snapToken = res.data.data.snap_token;
                        let namaGuest = res.data.data.nama_guest;
                        let orderId = res.data.data.order_id;

                        snap.pay(snapToken, {
                            onSuccess: function(result) {
                                window.location.href = `/customer/sukses/${orderId}`;
                            },
                            onPending: function(result) {
                                Swal.fire('Menunggu Pembayaran', `Halo ${namaGuest}, silakan masuk ke ATM/M-Banking Anda untuk menyelesaikan pembayaran.`, 'info')
                                    .then(() => window.location.reload());
                            },
                            onError: function(result) {
                                Swal.fire('Gagal', 'Pembayaran gagal dirute atau kedaluwarsa.', 'error');
                            },
                            onClose: function() {
                                Swal.fire('Dibatalkan', 'Anda menutup popup sebelum membayar. Pesanan ini tersimpan dan menunggu pembayaran.', 'warning')
                                    .then(() => window.location.reload());
                            }
                        });
                    } else {
                        Swal.fire('Gagal', res.data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Terjadi kesalahan sistem. Cek konsol!', 'error');
                });
        });
    </script>
</body>

</html>