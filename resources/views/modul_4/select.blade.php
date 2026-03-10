@extends('layouts.main')

@section('style-page')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
    .select2-container {
        width: 100% !important;
    }
</style>
@endsection

@section('content')
<div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Select</h4>
            </div>
            <div class="card-body">

                <form id="form-select-biasa">
                    <div class="form-group mb-3">
                        <label>Kota</label>
                        <input type="text" class="form-control" id="input-kota-biasa"
                            placeholder="Nama kota..." required>
                    </div>
                </form>

                <div class="mb-3">
                    <button type="button" id="btn-tambah-biasa" class="btn btn-gradient-primary w-100">
                        Tambahkan
                    </button>
                </div>

                <div class="form-group mb-3">
                    <label>Select Kota</label>
                    <select class="form-control" id="select-kota-biasa">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <div>
                    <label>Kota Terpilih:</label>
                    <span id="terpilih-biasa" class="fw-bold">-</span>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Select 2</h4>
            </div>
            <div class="card-body">

                <form id="form-select2">
                    <div class="form-group mb-3">
                        <label>Kota</label>
                        <input type="text" class="form-control" id="input-kota-select2"
                            placeholder="Nama kota..." required>
                    </div>
                </form>

                <div class="mb-3">
                    <button type="button" id="btn-tambah-select2" class="btn btn-gradient-primary w-100">
                        Tambahkan
                    </button>
                </div>

                <div class="form-group mb-3">
                    <label>Select Kota</label>
                    <select class="form-control" id="select-kota-select2">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <div>
                    <label>Kota Terpilih:</label>
                    <span id="terpilih-select2" class="fw-bold">-</span>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@section('script-page')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {

        $("#select-kota-select2").select2({
            theme: "bootstrap-5",
            placeholder: "-- Pilih Kota --"
        });

        $("#btn-tambah-biasa").click(function() {
            let form = $("#form-select-biasa")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let tombol = $(this);
            tombol.prop("disabled", true);
            tombol.html('<span class="spinner-border spinner-border-sm"></span> Menambahkan...');

            let kota = $("#input-kota-biasa").val();

            $("#select-kota-biasa").append(
                `<option value="${kota}">${kota}</option>`
            );

            $("#input-kota-biasa").val("");
            setTimeout(function() {
                tombol.prop("disabled", false);
                tombol.html("Tambahkan");
            }, 600);
        });

        $("#select-kota-biasa").on("change", function() {
            let terpilih = $(this).val();
            $("#terpilih-biasa").text(terpilih !== "" ? terpilih : "-");
        });

        $("#btn-tambah-select2").click(function() {
            let form = $("#form-select2")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let tombol = $(this);
            tombol.prop("disabled", true);
            tombol.html('<span class="spinner-border spinner-border-sm"></span> Menambahkan...');

            let kota = $("#input-kota-select2").val();

            let optionBaru = new Option(kota, kota, false, false);
            $("#select-kota-select2").append(optionBaru).trigger("change");

            $("#input-kota-select2").val("");
            setTimeout(function() {
                tombol.prop("disabled", false);
                tombol.html("Tambahkan");
            }, 600);
        });

        $("#select-kota-select2").on("change", function() {
            let terpilih = $(this).val();
            $("#terpilih-select2").text(terpilih !== "" ? terpilih : "-");
        });

    });
</script>
@endsection