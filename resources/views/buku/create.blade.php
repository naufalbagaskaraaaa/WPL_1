@extends('layouts.main')
@section('content')
<div class="card">
  <div class="card-body">
    <h4 class="card-title">Tambah Buku</h4>
    <p class="card-description">Silakan masukan nama buku baru</p>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success')}}
    </div>
    @endif

    <form id="form-tambah-buku" class="forms-sample" action="{{ route('buku.store') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="nama_kategori">Kategori</label>
        <select class="form-control" id="idkategori" name="idkategori" required>
          <option value="">pilih kategori</option>
          @foreach($kategori as $item)
          <option value="{{ $item->idkategori }}">
            {{ $item->nama_kategori }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="kode">Kode Buku</label>
        <input type="text" class="form-control" id="kode" name="kode"
          placeholder="kode bukunya jangan lupa!!" required>
      </div>

      <div class="form-group">
        <label for="judul">Judul Buku</label>
        <input type="text" class="form-control" id="judul" name="judul"
          placeholder="judulnya apa sayang?" required>
      </div>

      <div class="form-group">
        <label for="pengarang">Pengarang</label>
        <input type="text" class="form-control" id="pengarang" name="pengarang"
          placeholder="ini yang buat kamu bukan sih?" required>
      </div>
    </form>

    <div class="mt-3">
      <button type="button" id="btn-simpan-buku" class="btn btn-gradient-primary me-2">
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
    $("#btn-simpan-buku").click(function() {
      let form = $("#form-tambah-buku")[0];

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