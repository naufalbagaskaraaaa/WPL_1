@extends('layouts.main')
@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h4 class="card-title">Versi Ajax</h4>
        
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
        $(document).ready(function() {
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
                    console.log(error);
                }
            });

            $('#provinsi').on('change', function() {
                let id_provinsi = $(this).val();
                
                $('#kecamatan').html('<option value="0">Pilih Kecamatan</option>');
                $('#kelurahan').html('<option value="0">Pilih Kelurahan</option>');

                if (id_provinsi != 0) {
                    $.ajax({
                        method: "GET",
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
                            console.log(error);
                        }
                    });
                } else {
                    $('#kota').html('<option value="0">Pilih Kota</option>');
                }
            });

            $('#kota').on('change', function() {
                let id_kota = $(this).val();

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
                            console.log(error);
                        }
                    });
                } else {
                    $('#kecamatan').html('<option value="0">Pilih Kecamatan</option>');
                }
            });

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
                            console.log(error);
                        }
                    });
                } else {
                    $('#kelurahan').html('<option value="0">Pilih Kelurahan</option>');
                }
            });
        });
</script>
@endsection