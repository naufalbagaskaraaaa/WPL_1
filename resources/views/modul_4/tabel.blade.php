@extends('layouts.main')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h4 class="card-title">Tambah Barang</h4>
        <p class="card-description">Data tidak disimpan ke database</p>

        <form id="form-latihan">
            <div class="form-group">
                <label for="nama">Nama Barang</label>
                <input type="text" class="form-control" id="input-nama"
                    placeholder="Ketik nama barang..." required>
            </div>
            <div class="form-group">
                <label for="harga">Harga Barang</label>
                <input type="number" class="form-control" id="input-harga"
                    placeholder="Ketik harga barang..." min="0" required>
            </div>
        </form>

        <div class="mt-3">
            <button type="button" id="btn-tambah" class="btn btn-gradient-primary">
                Submit
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Daftar Barang</h4>
        <table class="table table-bordered" id="tabel-barang">
            <thead class="table-dark">
                <tr>
                    <th>ID Barang</th>
                    <th>Nama</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody id="tbody-barang" style="cursor: pointer"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-edit-hapus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit / Hapus Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-modal">
                    <div class="form-group mb-3">
                        <label>ID Barang</label>
                        {{-- readonly karena ID tidak boleh diubah --}}
                        <input type="text" class="form-control" id="modal-id" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" id="modal-nama" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Harga Barang</label>
                        <input type="number" class="form-control" id="modal-harga" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" id="btn-hapus">Hapus</button>
                <button type="button" class="btn btn-success" id="btn-ubah">Ubah</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script>
    $(document).ready(function() {

        let counter = 1;

        $("#btn-tambah").click(function() {
            let form = $("#form-latihan")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let tombol = $(this);
            tombol.prop("disabled", true);
            tombol.html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

            let nama = $("#input-nama").val();
            let harga = parseInt($("#input-harga").val());
            let idBaru = "BRG-" + String(counter).padStart(3, "0");
            let hargaFormat = "Rp " + harga.toLocaleString("id-ID");
            counter++;

            $("#tbody-barang").append(`
            <tr data-id="${idBaru}" data-nama="${nama}" data-harga="${harga}">
                <td>${idBaru}</td>
                <td>${nama}</td>
                <td>${hargaFormat}</td>
            </tr>
        `);

            $("#input-nama").val("");
            $("#input-harga").val("");

            setTimeout(function() {
                tombol.prop("disabled", false);
                tombol.html("Submit");
            }, 600);
        });

        $(document).on("click", "#tbody-barang tr", function() {

            $("#modal-id").val($(this).data("id"));
            $("#modal-nama").val($(this).data("nama"));
            $("#modal-harga").val($(this).data("harga"));

            $("#modal-edit-hapus").data("row-aktif", $(this));

            new bootstrap.Modal(document.getElementById("modal-edit-hapus")).show();
        });

        $("#btn-hapus").click(function() {

            let rowAktif = $("#modal-edit-hapus").data("row-aktif");

            rowAktif.remove();

            setTimeout(function() {
                bootstrap.Modal.getInstance(
                    document.getElementById("modal-edit-hapus")
                ).hide();

                tombol.prop("disabled", false);
                tombol.html("Ubah");
            }, 600)
        });

        $("#btn-ubah").click(function() {
            let form = $("#form-modal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let tombol = $(this);
            tombol.prop("disabled", true);
            tombol.html('<span class="spinner-border spinner-border-sm"></span> Mengubah...');

            let namaBaru = $("#modal-nama").val();
            let hargaBaru = parseInt($("#modal-harga").val());
            let hargaFmt = "Rp " + hargaBaru.toLocaleString("id-ID");
            let rowAktif = $("#modal-edit-hapus").data("row-aktif");

            rowAktif.find("td:nth-child(2)").text(namaBaru);
            rowAktif.find("td:nth-child(3)").text(hargaFmt);

            rowAktif.data("nama", namaBaru);
            rowAktif.data("harga", hargaBaru);

            setTimeout(function() {
                bootstrap.Modal.getInstance(
                    document.getElementById("modal-edit-hapus")
                ).hide();

                tombol.prop("disabled", false);
                tombol.html("Ubah");
            }, 600)
        });

    });
</script>
@endsection