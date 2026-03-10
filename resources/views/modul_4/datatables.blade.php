@extends('layouts.main')

@section('style-page')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h4 class="card-title">Tambah Barang</h4>
        <p class="card-description">Data tidak disimpan ke database</p>

        <form id="form-latihan-dt">
            <div class="form-group">
                <label for="input-nama-dt">Nama Barang</label>
                <input type="text" class="form-control" id="input-nama-dt"
                    placeholder="Ketik nama barang..." required>
            </div>
            <div class="form-group">
                <label for="input-harga-dt">Harga Barang</label>
                <input type="number" class="form-control" id="input-harga-dt"
                    placeholder="Ketik harga barang..." min="0" required>
            </div>
        </form>

        <div class="mt-3">
            <button type="button" id="btn-tambah-dt" class="btn btn-gradient-primary">
                Submit
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Daftar Barang</h4>
        <table class="table table-bordered" id="tabel-barang-dt" style="cursor: pointer">
            <thead class="table-dark">
                <tr>
                    <th>ID Barang</th>
                    <th>Nama</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-edit-hapus-dt" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit / Hapus Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-modal-dt">
                    <div class="form-group mb-3">
                        <label>ID Barang</label>
                        <input type="text" class="form-control" id="modal-id-dt" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" id="modal-nama-dt" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Harga Barang</label>
                        <input type="number" class="form-control" id="modal-harga-dt" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" id="btn-hapus-dt">Hapus</button>
                <button type="button" class="btn btn-success" id="btn-ubah-dt">Ubah</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {

        var dt = $("#tabel-barang-dt").DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Selanjutnya"
                },
                zeroRecords: "Belum ada data",
                emptyTable: "Belum ada data"
            }
        });

        let counter = 1;

        $("#btn-tambah-dt").click(function() {
            let form = $("#form-latihan-dt")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let tombol = $(this);
            tombol.prop("disabled", true);
            tombol.html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

            let nama = $("#input-nama-dt").val();
            let harga = parseInt($("#input-harga-dt").val());
            let idBaru = "BRG-" + String(counter).padStart(3, "0");
            let hargaFormat = "Rp " + harga.toLocaleString("id-ID");
            counter++;

            let rowNode = dt.row.add([idBaru, nama, hargaFormat]).draw().node();
            $(rowNode)
                .attr("data-id", idBaru)
                .attr("data-nama", nama)
                .attr("data-harga", harga);

            $("#input-nama-dt").val("");
            $("#input-harga-dt").val("");
            setTimeout(function() {
                bootstrap.Modal.getInstance(
                    document.getElementById("modal-edit-hapus")
                ).hide();

                tombol.prop("disabled", false);
                tombol.html("Ubah");
            }, 600);
        });

        $("#tabel-barang-dt tbody").on("click", "tr", function() {
            $("#modal-id-dt").val($(this).attr("data-id"));
            $("#modal-nama-dt").val($(this).attr("data-nama"));
            $("#modal-harga-dt").val($(this).attr("data-harga"));

            $("#modal-edit-hapus-dt").data("row-aktif", $(this));

            new bootstrap.Modal(document.getElementById("modal-edit-hapus-dt")).show();
        });

        $("#btn-hapus-dt").click(function() {
            let rowAktif = $("#modal-edit-hapus-dt").data("row-aktif");

            dt.row(rowAktif).remove().draw();

            bootstrap.Modal.getInstance(
                document.getElementById("modal-edit-hapus-dt")
            ).hide();
        });

        $("#btn-ubah-dt").click(function() {
            let form = $("#form-modal-dt")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let tombol = $(this);
            tombol.prop("disabled", true);
            tombol.html('<span class="spinner-border spinner-border-sm"></span> Mengubah...');

            let idLama = $("#modal-id-dt").val();
            let namaBaru = $("#modal-nama-dt").val();
            let hargaBaru = parseInt($("#modal-harga-dt").val());
            let hargaFmt = "Rp " + hargaBaru.toLocaleString("id-ID");
            let rowAktif = $("#modal-edit-hapus-dt").data("row-aktif");

            let rowNode = dt.row(rowAktif).data([idLama, namaBaru, hargaFmt]).draw().node();

            $(rowNode)
                .attr("data-nama", namaBaru)
                .attr("data-harga", hargaBaru);

            bootstrap.Modal.getInstance(
                document.getElementById("modal-edit-hapus-dt")
            ).hide();

            tombol.prop("disabled", false);
            tombol.html("Ubah");
        });

    });
</script>
@endsection