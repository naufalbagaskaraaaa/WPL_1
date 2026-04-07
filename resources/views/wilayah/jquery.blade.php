<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cascading Dropdown - AJAX jQuery</title>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select { width: 100%; max-width: 300px; padding: 8px; }
    </style>
</head>
<body>
    <h2>Versi A: Menggunakan AJAX jQuery</h2>

    <div class="form-group">
        <label for="provinsi">Provinsi</label>
        <select id="provinsi" name="provinsi">
            <option value="0">Pilih Provinsi</option>
        </select>
    </div>

    <div class="form-group">
        <label for="kota">Kota/Kabupaten</label>
        <select id="kota" name="kota">
            <option value="0">Pilih Kota</option>
        </select>
    </div>

    <div class="form-group">
        <label for="kecamatan">Kecamatan</label>
        <select id="kecamatan" name="kecamatan">
            <option value="0">Pilih Kecamatan</option>
        </select>
    </div>

    <div class="form-group">
        <label for="kelurahan">Kelurahan/Desa</label>
        <select id="kelurahan" name="kelurahan">
            <option value="0">Pilih Kelurahan</option>
        </select>
    </div>

    <script>
        $(document).ready(function() {
            // 1. Load data Provinsi saat halaman pertama kali dibuka
            $.ajax({
                method: "GET",
                url: "{{ route('wilayah.provinces') }}",
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="0">Pilih Provinsi</option>';
                        response.data.forEach(function(item) {
                            options += `<option value="${item.id}">${item.name}</option>`;
                        });
                        $('#provinsi').html(options);
                    }
                },
                error: function(error) {
                    console.log("Error loading provinces:", error);
                }
            });

            // 2. Ketika Provinsi berubah, load data Kota
            $('#provinsi').on('change', function() {
                let id_provinsi = $(this).val();
                
                // Kosongkan opsi Kecamatan dan Kelurahan sesuai ketentuan
                $('#kecamatan').html('<option value="0">Pilih Kecamatan</option>');
                $('#kelurahan').html('<option value="0">Pilih Kelurahan</option>');

                if (id_provinsi != 0) {
                    $.ajax({
                        method: "GET",
                        // Ganti parameter dinamis menggunakan string replace atau konkatensi
                        url: "/wilayah/regencies/" + id_provinsi,
                        success: function(response) {
                            if (response.status === 'success') {
                                let options = '<option value="0">Pilih Kota</option>';
                                response.data.forEach(function(item) {
                                    options += `<option value="${item.id}">${item.name}</option>`;
                                });
                                $('#kota').html(options);
                            }
                        },
                        error: function(error) {
                            console.log("Error loading regencies:", error);
                        }
                    });
                } else {
                    // Jika kembali ke "Pilih Provinsi", kosongkan opsi Kota juga
                    $('#kota').html('<option value="0">Pilih Kota</option>');
                }
            });

            // 3. Ketika Kota berubah, load data Kecamatan
            $('#kota').on('change', function() {
                let id_kota = $(this).val();

                // Kosongkan opsi Kelurahan
                $('#kelurahan').html('<option value="0">Pilih Kelurahan</option>');

                if (id_kota != 0) {
                    $.ajax({
                        method: "GET",
                        url: "/wilayah/districts/" + id_kota,
                        success: function(response) {
                            if (response.status === 'success') {
                                let options = '<option value="0">Pilih Kecamatan</option>';
                                response.data.forEach(function(item) {
                                    options += `<option value="${item.id}">${item.name}</option>`;
                                });
                                $('#kecamatan').html(options);
                            }
                        },
                        error: function(error) {
                            console.log("Error loading districts:", error);
                        }
                    });
                } else {
                    $('#kecamatan').html('<option value="0">Pilih Kecamatan</option>');
                }
            });

            // 4. Ketika Kecamatan berubah, load data Kelurahan
            $('#kecamatan').on('change', function() {
                let id_kecamatan = $(this).val();

                if (id_kecamatan != 0) {
                    $.ajax({
                        method: "GET",
                        url: "/wilayah/villages/" + id_kecamatan,
                        success: function(response) {
                            if (response.status === 'success') {
                                let options = '<option value="0">Pilih Kelurahan</option>';
                                response.data.forEach(function(item) {
                                    options += `<option value="${item.id}">${item.name}</option>`;
                                });
                                $('#kelurahan').html(options);
                            }
                        },
                        error: function(error) {
                            console.log("Error loading villages:", error);
                        }
                    });
                } else {
                    $('#kelurahan').html('<option value="0">Pilih Kelurahan</option>');
                }
            });
        });
    </script>
</body>
</html>