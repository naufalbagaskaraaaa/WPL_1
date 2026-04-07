@extends('layouts.main')

@section('content')
<style>
    .wilayah-wrapper {
        font-family: inherit;
        background: #fff;
        padding: 30px;
        color: #000;
        max-width: 500px;
        margin: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05); /* Subtle shadow for card effect */
    }
    .form-row {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    .form-row label {
        width: 120px;
        font-weight: normal;
        margin: 0;
        font-size: 14px;
        color: #000;
    }
    .input-wrapper {
        flex: 1;
    }
    .form-row select {
        width: 100%;
        padding: 6px 12px;
        border: 1px solid #3b5998;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
        background-color: white;
        color: #000;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%233b5998%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
        background-repeat: no-repeat;
        background-position: right 10px top 50%;
        background-size: 12px auto;
    }
</style>

<div class="wilayah-wrapper border-0">
    <div class="form-row">
        <label for="provinsi">Provinsi</label>
        <div class="input-wrapper">
            <select id="provinsi" name="provinsi">
                <option value="0">Pilih Provinsi</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <label for="kota">Kota</label>
        <div class="input-wrapper">
            <select id="kota" name="kota">
                <option value="0">Pilih Kota</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <label for="kecamatan">Kecamatan</label>
        <div class="input-wrapper">
            <select id="kecamatan" name="kecamatan">
                <option value="0">Pilih Kecamatan</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <label for="kelurahan">Kelurahan</label>
        <div class="input-wrapper">
            <select id="kelurahan" name="kelurahan">
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

        axios({
            method: 'GET',
            url: "{{ route('wilayah.provinces') }}"
        })
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
                axios({
                    method: 'GET',
                    url: "/wilayah/regencies/" + id_provinsi
                })
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
                axios({
                    method: 'GET',
                    url: "/wilayah/districts/" + id_kota
                })
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
                axios({
                    method: 'GET',
                    url: "/wilayah/villages/" + id_kecamatan
                })
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

