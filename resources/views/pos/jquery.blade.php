@extends('layouts.main')

@section('content')
<style>
    .pos-wrapper {
        font-family: sans-serif;
        background: #fff;
        padding: 30px;
        color: #000;
        min-height: 100vh;
    }
    .form-row {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        max-width: 700px;
    }
    .form-row label {
        width: 180px;
        font-weight: normal;
        margin: 0;
        font-size: 14px;
        color: #000;
    }
    .input-wrapper {
        flex: 1;
    }
    .form-row input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #3b5998;
        border-radius: 2px;
        box-sizing: border-box;
        font-size: 14px;
    }
    .form-row input[readonly], .form-row input:disabled {
        background-color: #feebe6;
        border: 1px solid #ff0000;
    }
    .btn-container {
        display: flex;
        justify-content: flex-end;
        max-width: 700px;
        margin-bottom: 30px;
    }
    .btn-green {
        background-color: #00b050;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: normal;
        font-size: 14px;
    }
    .btn-green:disabled {
        background-color: #79c593;
        cursor: not-allowed;
    }
    table {
        width: 100%;
        max-width: 800px;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    table, th, td {
        border: 1px solid #000;
    }
    th, td {
        padding: 5px 8px;
        text-align: left;
        font-size: 14px;
        color: #000;
    }
    th {
        font-weight: bold;
    }
    .bayar-btn-container {
        width: 100%;
        max-width: 800px;
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }
    .total-title {
        font-weight: bold;
        text-align: center;
    }
    .td-no-border {
        border: none !important;
    }
    .jumlah-input {
        width: 60px;
        padding: 4px;
        border: 1px solid #000;
    }
</style>

<div class="pos-wrapper border-0">
    <div class="form-row">
        <label>Kode barang :</label>
        <div class="input-wrapper">
            <input type="text" id="kode_barang">
        </div>
    </div>

    <div class="form-row">
        <label>Nama barang :</label>
        <div class="input-wrapper">
            <input type="text" id="nama_barang" readonly disabled>
        </div>
    </div>

    <div class="form-row">
        <label>Harga barang :</label>
        <div class="input-wrapper">
            <input type="number" id="harga_barang" readonly disabled>
        </div>
    </div>

    <div class="form-row">
        <label>Jumlah:</label>
        <div class="input-wrapper">
            <input type="number" id="jumlah_barang" value="">
        </div>
    </div>

    <div class="btn-container">
        <button id="btn-tambahkan" class="btn-green" disabled>Tambahkan</button>
    </div>

    <table id="tabel-keranjang">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
        <tfoot id="table-footer" style="display:none;">
            <tr>
                <td colspan="3" class="td-no-border"></td>
                <td class="td-no-border total-title">Total</td>
                <td class="td-no-border" style="font-weight:bold" id="total-html"></td>
            </tr>
        </tfoot>
    </table>

    <div class="bayar-btn-container">
        <button id="btn-bayar" style="display:none;" class="btn-green" disabled>Bayar</button>
    </div>
</div>
@endsection

@section('script-page')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let cart = [];

        $('#kode_barang').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                let kode = $(this).val().trim();

                if (!kode) return;

                $.ajax({
                    url: "/api/barang/" + kode,
                    type: "GET",
                    success: function(response) {
                        if (response.status === 'success') {
                            let dataBarang = response.data;
                            $('#nama_barang').val(dataBarang.nama);
                            $('#harga_barang').val(dataBarang.harga);       
                            $('#jumlah_barang').val(1);
                            checkTombolTambahkan();
                            $('#jumlah_barang').focus();
                        }
                    },
                    error: function(xhr) {
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

        $('#btn-tambahkan').on('click', function() {
            let id_barang = $('#kode_barang').val();
            let nama = $('#nama_barang').val();
            let harga = parseInt($('#harga_barang').val());
            let jumlah = parseInt($('#jumlah_barang').val());

            if (jumlah < 1) {
                Swal.fire('Peringatan', 'Jumlah minimum adalah 1', 'warning');
                return;
            }

            let existIndex = cart.findIndex(item => item.id_barang === id_barang);

            if (existIndex > -1) {
                cart[existIndex].jumlah += jumlah;
                cart[existIndex].subtotal = cart[existIndex].jumlah * cart[existIndex].harga;
            } else {
                cart.push({
                    id_barang: id_barang,
                    nama: nama,
                    harga: harga,
                    jumlah: jumlah,
                    subtotal: harga * jumlah
                });
            }

            renderTable();
            resetFormInput();
        });

        function renderTable() {
            let tbody = $('#tabel-keranjang tbody');
            let tfoot = $('#table-footer');
            tbody.empty();
            let total = 0;

            if (cart.length === 0) {
                tbody.html('<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>');
                tfoot.hide();
                $('#btn-bayar').hide().prop('disabled', true);
                return;
            }

            $.each(cart, function(index, item) {
                total += item.subtotal;
                let tr = `
                    <tr>
                        <td>${item.id_barang}</td>
                        <td>${item.nama}</td>
                        <td>${item.harga}</td>
                        <td>
                            <input type="number" class="jumlah-input" data-index="${index}" value="${item.jumlah}" min="0">
                        </td>
                        <td>${item.subtotal}</td>
                    </tr>
                `;
                tbody.append(tr);
            });

            $('#total-html').text(total);
            tfoot.show();
            $('#btn-bayar').show().prop('disabled', false);
        }

        $('#tabel-keranjang').on('input', '.jumlah-input', function() {
            let index = $(this).data('index');
            let newJumlah = parseInt($(this).val());

            if(newJumlah === 0) {
                cart.splice(index, 1);
                renderTable();
            } else if (newJumlah > 0) {
                cart[index].jumlah = newJumlah;
                cart[index].subtotal = cart[index].harga * newJumlah;
                renderTable();
            }
        });

        function resetFormInput() {
            $('#kode_barang').val('');
            $('#nama_barang').val('');
            $('#harga_barang').val('');
            $('#jumlah_barang').val('');
            checkTombolTambahkan();
            $('#kode_barang').focus();
        }

        $('#btn-bayar').on('click', function() {
            let totalPembayaran = cart.reduce((sum, item) => sum + item.subtotal, 0);

            $.ajax({
                url: "{{ route('api.penjualan.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    items: cart,
                    total: totalPembayaran
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Sukses!',
                            text: "ID Penjualan: " + response.data.id_penjualan
                        }).then((result) => {
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
@endsection