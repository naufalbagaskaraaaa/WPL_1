@extends('layouts.main')
@section('content')
<div class="card">
  <div class="card-body">
    <h4 class="card-title">Tambah Kategori</h4>
    <p class="card-description">Silakan masukan kategori buku</p>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success')}}
    </div>
    @endif

    <form id="form-tambah-kategori" class="forms-sample" action="{{ route('kategori.store') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="nama_kategori">Nama Kategori</label>
        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
          placeholder="apa kategori buku itu?" required>
      </div>
    </form>

    <div class="mt-3">
      <button type="button" id="btn-simpan-kategori" class="btn btn-gradient-primary me-2">
        Simpan
      </button>
      <a href="{{ route('dashboard') }}" class="btn btn-light">
        Batal
      </a>
    </div>
  </div>
</div>
@endsection

@section('script-page')
<script>
  $(document).ready(function() {
    $("#btn-simpan-kategori").click(function() {
      let form = $("#form-tambah-kategori")[0];

      if (!form.checkValidity()) {
        form.reportValidity();
      } else {
        let tombol = $(this);
        tombol.prop("disabled", true);
        tombol.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        form.submit();
      }
    });
  });
</script>
@endsection