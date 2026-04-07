@extends('layouts.main')
@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h4 class="card-title">Versi Axios</h4>

        <div class="form-group mb-3">
            <label for="provinsi">Provinsi</label>
            <select id="provinsi" name="provinsi" class="form-control" style="width: 100%; max-width: 300px;">
                <option value="0">Pilih Provinsi</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="kota">Kota/Kabupaten</label>
            <select id="kota" name="kota" class="form-control" style="width: 100%; max-width: 300px;">
                <option value="0">Pilih Kota</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="kecamatan">Kecamatan</label>
            <select id="kecamatan" name="kecamatan" class="form-control" style="width: 100%; max-width: 300px;">
                <option value="0">Pilih Kecamatan</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="kelurahan">Kelurahan/Desa</label>
            <select id="kelurahan" name="kelurahan" class="form-control" style="width: 100%; max-width: 300px;">
                <option value="0">Pilih Kelurahan</option>
            </select>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinsiSelect = document.getElementById('provinsi');
        const kotaSelect = document.getElementById('kota');
        const kecamatanSelect = document.getElementById('kecamatan');
        const kelurahanSelect = document.getElementById('kelurahan');

        const buildOptions = (data, defaultText) => {
            let options = `<option value="0">${defaultText}</option>`;
            data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            return options;
        };

        axios.get("{{ route('wilayah.provinces') }}")
            .then(function(response) {
                const res = response.data;
                if (res.status === 'success') {
                    provinsiSelect.innerHTML = buildOptions(res.data, 'Pilih Provinsi');
                }
            })
            .catch(function(error) {
                console.log(error);
            });

        provinsiSelect.addEventListener('change', function() {
            const id_provinsi = this.value;

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
                        console.log(error);
                    });
            } else {
                kotaSelect.innerHTML = '<option value="0">Pilih Kota</option>';
            }
        });

        kotaSelect.addEventListener('change', function() {
            const id_kota = this.value;

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
                        console.log(error);
                    });
            } else {
                kecamatanSelect.innerHTML = '<option value="0">Pilih Kecamatan</option>';
            }
        });

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
                        console.log(error);
                    });
            } else {
                kelurahanSelect.innerHTML = '<option value="0">Pilih Kelurahan</option>';
            }
        });
    });
</script>
@endsection