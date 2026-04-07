<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cascading Dropdown - Axios</title>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select { width: 100%; max-width: 300px; padding: 8px; }
    </style>
</head>
<body>
    <h2>Versi B: Menggunakan Axios</h2>

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
        document.addEventListener('DOMContentLoaded', function() {
            const provinsiSelect = document.getElementById('provinsi');
            const kotaSelect = document.getElementById('kota');
            const kecamatanSelect = document.getElementById('kecamatan');
            const kelurahanSelect = document.getElementById('kelurahan');

            // Fungsi untuk membuat elemen option
            const buildOptions = (data, defaultText) => {
                let options = `<option value="0">${defaultText}</option>`;
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                return options;
            };

            // 1. Load data Provinsi
            axios.get("{{ route('wilayah.provinces') }}")
                .then(function(response) {
                    // Axios membungkus data di dalam response.data
                    const res = response.data;
                    if (res.status === 'success') {
                        provinsiSelect.innerHTML = buildOptions(res.data, 'Pilih Provinsi');
                    }
                })
                .catch(function(error) {
                    console.log("Error loading provinces:", error);
                });

            // 2. Ketika Provinsi berubah, load data Kota
            provinsiSelect.addEventListener('change', function() {
                const id_provinsi = this.value;
                
                // Kosongkan opsi Kecamatan dan Kelurahan sesuai ketentuan
                kecamatanSelect.innerHTML = '<option value="0">Pilih Kecamatan</option>';
                kelurahanSelect.innerHTML = '<option value="0">Pilih Kelurahan</option>';

                if (id_provinsi != 0) {
                    axios.get("/wilayah/regencies/" + id_provinsi)
                        .then(function(response) {
                            const res = response.data;
                            if (res.status === 'success') {
                                kotaSelect.innerHTML = buildOptions(res.data, 'Pilih Kota');
                            }
                        })
                        .catch(function(error) {
                            console.log("Error loading regencies:", error);
                        });
                } else {
                    kotaSelect.innerHTML = '<option value="0">Pilih Kota</option>';
                }
            });

            // 3. Ketika Kota berubah, load data Kecamatan
            kotaSelect.addEventListener('change', function() {
                const id_kota = this.value;

                // Kosongkan opsi Kelurahan
                kelurahanSelect.innerHTML = '<option value="0">Pilih Kelurahan</option>';

                if (id_kota != 0) {
                    axios.get("/wilayah/districts/" + id_kota)
                        .then(function(response) {
                            const res = response.data;
                            if (res.status === 'success') {
                                kecamatanSelect.innerHTML = buildOptions(res.data, 'Pilih Kecamatan');
                            }
                        })
                        .catch(function(error) {
                            console.log("Error loading districts:", error);
                        });
                } else {
                    kecamatanSelect.innerHTML = '<option value="0">Pilih Kecamatan</option>';
                }
            });

            // 4. Ketika Kecamatan berubah, load data Kelurahan
            kecamatanSelect.addEventListener('change', function() {
                const id_kecamatan = this.value;

                if (id_kecamatan != 0) {
                    axios.get("/wilayah/villages/" + id_kecamatan)
                        .then(function(response) {
                            const res = response.data;
                            if (res.status === 'success') {
                                kelurahanSelect.innerHTML = buildOptions(res.data, 'Pilih Kelurahan');
                            }
                        })
                        .catch(function(error) {
                            console.log("Error loading villages:", error);
                        });
                } else {
                    kelurahanSelect.innerHTML = '<option value="0">Pilih Kelurahan</option>';
                }
            });
        });
    </script>
</body>
</html>