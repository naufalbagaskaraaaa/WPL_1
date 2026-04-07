<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aplikasi Kasir (POS) - jQuery</title>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-container, .tabel-container { margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: inline-block; width: 120px; font-weight: bold; }
        .form-group input { padding: 5px; width: 250px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; text-align: left; }
        th, td { padding: 8px; }
        .btn { padding: 8px 15px; cursor: pointer; }
        .btn-blue { background-color: blue; color: white; border: none; }
        .btn-green { background-color: green; color: white; border: none; }
        .btn-red { background-color: red; color: white; border: none; }
        .jumlah-input { width: 60px; }
    </style>
</head>
<body>

    <h2>Point of Sales (Kasir) - Versi jQuery</h2>

    <div class="form-container">
        <h3>Input Barang</h3>
        <div class="form-group">
            <label>Kode Barang:</label>
            <input type="text" id="kode_barang" placeholder="Ketik kode & tekan Enter">
        </div>
        <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" id="nama_barang" readonly placeholder="Otomatis">
        </div>
        <div class="form-group">
            <label>Harga Barang:</label>
            <input type="number" id="harga_barang" readonly placeholder="Otomatis">
        </div>
        <div class="form-group">
            <label>Jumlah:</label>
            <input type="number" id="jumlah_barang" value="1" min="1">
        </div>
        <button id="btn-tambahkan" class="btn btn-blue" disabled>Tambahkan</button>
    </div>

    <div class="tabel-container">
        <h3>Keranjang Belanja</h3>
        <table id="tabel-keranjang">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data masuk ke sini -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align: right;">Total Keseluruhan</th>
                    <th id="total-html">Rp 0</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <br>
        <button id="btn-bayar" class="btn btn-green" disabled>Bayar & Simpan</button>
    </div>

    <script>
        $(document).ready(function() {
            // State Management Array Keranjang
            let cart = [];

            // 1. Pencarian Barang saat mengetik Kode dan menekan Enter
            $('#kode_barang').on('keypress', function(e) {
                if (e.which === 13) { // 13 adalah kode tombol Enter
                    e.preventDefault();
                    let kode = $(this).val();

                    if (!kode) return;

                    $.ajax({
                        url: "/api/barang/" + kode,
                        method: "GET",
                        success: function(response) {
                            if (response.status === 'success') {
                                let dataBarang = response.data;
                                $('#nama_barang').val(dataBarang.nama);
                                $('#harga_barang').val(dataBarang.harga);
                                $('#jumlah_barang').val(1);
                                checkTombolTambahkan(); // Cek state tombol
                                $('#jumlah_barang').focus(); // Otomatis ke field jumlah
                            }
                        },
                        error: function(xhr) {
                            // Mengembalikan inputan menjadi default
                            $('#nama_barang').val('');
                            $('#harga_barang').val('');
                            $('#btn-tambahkan').prop('disabled', true);
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Barang tidak ditemukan di database!'
                            });
                        }
                    });
                }
            });

            // Fungsi Validasi Tombol "Tambahkan" Real-time
            function checkTombolTambahkan() {
                let nama = $('#nama_barang').val();
                let jumlah = parseInt($('#jumlah_barang').val());
                if (nama.trim() !== '' && !isNaN(jumlah) && jumlah > 0) {
                    $('#btn-tambahkan').prop('disabled', false);
                } else {
                    $('#btn-tambahkan').prop('disabled', true);
                }
            }

            $('#jumlah_barang').on('input change', function() {
                checkTombolTambahkan();
            });

            // 2. Klik Tambahkan ke tabel
            $('#btn-tambahkan').on('click', function() {
                let id_barang = $('#kode_barang').val();
                let nama = $('#nama_barang').val();
                let harga = parseInt($('#harga_barang').val());
                let jumlah = parseInt($('#jumlah_barang').val());

                if (jumlah < 1) {
                    Swal.fire('Peringatan', 'Jumlah harus lebih besar dari 0', 'warning');
                    return;
                }

                // Cek apakah barang sudah ada di keranjang
                let existIndex = cart.findIndex(item => item.id_barang === id_barang);

                if (existIndex > -1) {
                    // Update jumlah dan subtotal
                    cart[existIndex].jumlah += jumlah;
                    cart[existIndex].subtotal = cart[existIndex].jumlah * cart[existIndex].harga;
                } else {
                    // Tambah data baru array
                    cart.push({
                        id_barang: id_barang,
                        nama: nama,
                        harga: harga,
                        jumlah: jumlah,
                        subtotal: harga * jumlah
                    });
                }

                renderTable(); // Update UI
                resetFormInput(); // Bersihkan form
            });

            // Render Table (Tampilkan List)
            function renderTable() {
                let tbody = $('#tabel-keranjang tbody');
                tbody.empty();
                let total = 0;

                cart.forEach((item, index) => {
                    total += item.subtotal;
                    let tr = `
                        <tr>
                            <td>${item.id_barang}</td>
                            <td>${item.nama}</td>
                            <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                            <td>
                                <input type="number" class="jumlah-input" data-index="${index}" value="${item.jumlah}" min="1">
                            </td>
                            <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                            <td>
                                <button class="btn btn-red btn-hapus" data-index="${index}">Hapus</button>
                            </td>
                        </tr>
                    `;
                    tbody.append(tr);
                });

                $('#total-html').text('Rp ' + total.toLocaleString('id-ID'));
                
                // Aktif/nonaktif tombol bayar
                if (cart.length > 0) {
                    $('#btn-bayar').prop('disabled', false);
                } else {
                    $('#btn-bayar').prop('disabled', true);
                }
            }

            // Fungsi Ubah Jumlah Langsung dari Tabel
            $('#tabel-keranjang').on('change', '.jumlah-input', function() {
                let index = $(this).data('index');
                let newJumlah = parseInt($(this).val());

                if(newJumlah > 0) {
                    cart[index].jumlah = newJumlah;
                    cart[index].subtotal = cart[index].harga * newJumlah;
                    renderTable();
                } else {
                    $(this).val(1); // Force reset to 1
                }
            });

            // Fungsi Hapus Barisan di Tabel
            $('#tabel-keranjang').on('click', '.btn-hapus', function() {
                let index = $(this).data('index');
                cart.splice(index, 1); // Buang 1 array list
                renderTable();
            });

            // Membersihkan Input Form
            function resetFormInput() {
                $('#kode_barang').val('');
                $('#nama_barang').val('');
                $('#harga_barang').val('');
                $('#jumlah_barang').val(1);
                checkTombolTambahkan();
                $('#kode_barang').focus(); // Kembali fokus ke pengetikan kode barang
            }

            // 3. Proses Pembayaran
            $('#btn-bayar').on('click', function() {
                let totalPembayaran = cart.reduce((sum, item) => sum + item.subtotal, 0);

                $.ajax({
                    url: "{{ route('api.penjualan.store') }}",
                    method: "POST", // Metode POST WAJIB Menyertakan token 
                    data: {
                        _token: "{{ csrf_token() }}", // Sesuai instruksi modul (dikirim via payload body)
                        items: cart,
                        total: totalPembayaran
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Transaksi Sukses!',
                                text: `ID Penjualan: ${response.data.id_penjualan}`
                            }).then((result) => {
                                // Refresh keranjang jadi 0
                                cart = [];
                                renderTable();
                            });
                        }
                    },
                    error: function(xhr) {
                        let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Gagal menyimpan transaksi.";
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: msg
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>