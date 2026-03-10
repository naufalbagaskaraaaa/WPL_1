@extends('layouts.main')
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Edit Barang</h4>
        <p class="card-description">Silakan ubah data barang</p>

        <form id="form-edit-barang" class="forms-sample" action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="id_barang">ID Barang</label>
                <input type="text" class="form-control" id="id_barang" value="{{ $barang->id_barang }}" disabled>
            </div>

            <div class="form-group">
                <label for="nama">Nama Barang</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Ketik nama barangnya di sini..." value="{{ old('nama', $barang->nama) }}" required>
                @error('nama')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" placeholder="Berapa harganya?" value="{{ old('harga', $barang->harga) }}" min="0" required>
                @error('harga')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </form>
        <div class="mt-3">
            <button type="button" id="btn-update" class="btn btn-gradient-primary me-2">
                Update
            </button>
            <a href="{{ route('barang.index') }}" class="btn btn-light">
                Batal
            </a>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script>
    $(document).ready(function() {
        $("#btn-update").click(function() {
            let form = $("#form-edit-barang")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {
                let tombol = $(this);
                tombol.prop("disabled", true);
                tombol.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengupdate...');
                form.submit();
            }
        });
    });
</script>
@endsection