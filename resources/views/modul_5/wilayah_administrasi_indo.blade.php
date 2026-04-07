@extends('layouts.main')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Data Wilayah Administrasi</h5>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label for="provinsi" class="form-label">Provinsi</label>
                        <select class="form-select" id="provinsi" name="provinsi">
                            <option value="0">Pilih Provinsi</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="kota" class="form-label">Kota/Kabupaten</label>
                        <select class="form-select" id="kota" name="kota" disabled>
                            <option value="0">Pilih Kota</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="kecamatan" class="form-label">Kecamatan</label>
                        <select class="form-select" id="kecamatan" name="kecamatan" disabled>
                            <option value="0">Pilih Kecamatan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="kelurahan" class="form-label">Kelurahan</label>
                        <select class="form-select" id="kelurahan" name="kelurahan" disabled>
                            <option value="0">Pilih Kelurahan</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script-page')
<script>
    $(document).ready(function() {

        axios.get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
            .then(function(response) {
                let dataProvinsi = response.data;
                let opsiProvinsi = '';

                dataProvinsi.forEach(function(prov) {
                    opsiProvinsi += `<option value="${prov.id}">${prov.name}</option>`;
                });

                $('#provinsi').append(opsiProvinsi);
            })
            .catch(function(error) {
                console.log("Terjadi kesalahan saat mengambil data provinsi:", error);
            });

    });
</script>
@endsection