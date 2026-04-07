<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aplikasi Kasir (POS) - Axios</title>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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

    <h2>Point of Sales (Kasir) - Versi Axios Promise</h2>

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
                <!-- Data List Keranjang... -->
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
        document.addEventListener('DOMContentLoaded', function() {
            // Setup CSRF token Axios secara global
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

            // DOM Elements
            const inputKode = document.getElementById('kode_barang');
            const inputNama = document.getElementById('nama_barang');
            const inputHarga = document.getElementById('harga_barang');
            const inputJumlah = document.getElementById('jumlah_barang');
            const btnTambahkan = document.getElementById('btn-tambahkan');
            const tbodyKeranjang = document.querySelector('#tabel-keranjang tbody');
            const totalHtml = document.getElementById('total-html');
            const btnBayar = document.getElementById('btn-bayar');

            // Array Penampung Transaksi
            let cart = [];

            // 1. Pencarian Barang via Axios
            inputKode.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let kode = this.value.trim();

                    if (!kode) return;

                    axios.get("/api/barang/" + kode)
                        .then(function(response) {
                            // Konsep Promise: Jika berhasil (Status HTTP 200/201)
                            const res = response.data;
                            if (res.status === 'success') {
                                inputNama.value = res.data.nama;
                                inputHarga.value = res.data.harga;
                                inputJumlah.value = 1;
                                checkTombolTambahkan(); // Buka Gembok Sesuai Aturan
                                inputJumlah.focus();
                            }
                        })
                        .catch(function(error) {
                            // Konsep Promise: Jika gagal (HTTP >= 400)
                            inputNama.value = '';
                            inputHarga.value = '';
                            btnTambahkan.disabled = true;

                            let pesanError = "Barang tidak ditemukan di database!";
                            if (error.response && error.response.data.message) {
                                pesanError = error.response.data.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: pesanError
                            });
                        });
                }
            });

            // Fungsi Validasi Tombol "Tambahkan" Real-time
            function checkTombolTambahkan() {
                let nama = inputNama.value;
                let jumlah = parseInt(inputJumlah.value);
                if (nama.trim() !== '' && !isNaN(jumlah) && jumlah > 0) {
                    btnTambahkan.disabled = false;
                } else {
                    btnTambahkan.disabled = true;
                }
            }

            inputJumlah.addEventListener('input', checkTombolTambahkan);
            inputJumlah.addEventListener('change', checkTombolTambahkan);

            // 2. Klik Tambahkan ke Tabel Memory
            btnTambahkan.addEventListener('click', function() {
                let id_barang = inputKode.value;
                let nama = inputNama.value;
                let harga = parseInt(inputHarga.value);
                let jumlah = parseInt(inputJumlah.value);

                if (jumlah < 1) {
                    Swal.fire('Peringatan', 'Jumlah minimum adalah 1', 'warning');
                    return;
                }

                // Cek apakah barang sudah ada di keranjang menggunakan method array findIndex
                let existIndex = cart.findIndex(item => item.id_barang === id_barang);

                if (existIndex > -1) {
                    // Update existing
                    cart[existIndex].jumlah += jumlah;
                    cart[existIndex].subtotal = cart[existIndex].jumlah * cart[existIndex].harga;
                } else {
                    // Masukkan barisan baru object JS
                    cart.push({
                        id_barang: id_barang,
                        nama: nama,
                        harga: harga,
                        jumlah: jumlah,
                        subtotal: harga * jumlah
                    });
                }

                renderTable(); // Update Visual HTML
                resetFormInput(); // Kosongkan Input Field Kasir
            });

            // Fungsi Render Visual HTML (Data Binding)
            function renderTable() {
                tbodyKeranjang.innerHTML = '';
                let total = 0;

                cart.forEach((item, index) => {
                    total += item.subtotal;
                    let tr = document.createElement('tr');
                    tr.innerHTML = `
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
                    `;
                    tbodyKeranjang.appendChild(tr);
                });

                totalHtml.innerText = 'Rp ' + total.toLocaleString('id-ID'); // Totalkan

                // Gembok / Buka Tombol Bayar
                btnBayar.disabled = (cart.length === 0);
            }

            // Mencegah Perilaku Langsung (Event Delegation Native JS)
            tbodyKeranjang.addEventListener('input', function(e) {
                if (e.target.classList.contains('jumlah-input')) {
                    let index = parseInt(e.target.getAttribute('data-index'));
                    let newJumlah = parseInt(e.target.value);

                    if(newJumlah > 0) {
                        cart[index].jumlah = newJumlah;
                        cart[index].subtotal = cart[index].harga * newJumlah;
                        renderTable();
                    }
                }
            });

            tbodyKeranjang.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-hapus')) {
                    let index = parseInt(e.target.getAttribute('data-index'));
                    cart.splice(index, 1);
                    renderTable();
                }
            });

            // Membersihkan Input Fields
            function resetFormInput() {
                inputKode.value = '';
                inputNama.value = '';
                inputHarga.value = '';
                inputJumlah.value = 1;
                checkTombolTambahkan();
                inputKode.focus();
            }

            // 3. Eksekutor API Bayar
            btnBayar.addEventListener('click', function() {
                // Hitung total dengan map reduce 
                let totalPembayaran = cart.reduce((sum, item) => sum + item.subtotal, 0);

                // POST method menggunakan Axios (Data Raw JS Object langsung dikirim, tidak seperti stringify)
                axios.post("{{ route('api.penjualan.store') }}", {
                    items: cart,
                    total: totalPembayaran
                })
                .then(function(response) {
                    const res = response.data;
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Sukses!',
                            text: "ID Penjualan disimpan di urutan Database: " + res.data.id_penjualan
                        }).then(() => {
                            // Clear System 
                            cart = [];
                            renderTable();
                        });
                    }
                })
                .catch(function(error) {
                    let msg = "Gagal menyimpan transaksi ke dalam database POSTgreSQL.";
                    if (error.response && error.response.data.message) {
                        msg = error.response.data.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: msg
                    });
                });
            });
        });
    </script>
</body>
</html>