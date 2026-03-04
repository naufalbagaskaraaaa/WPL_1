@extends('layouts.main')
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Tambah Barang</h4>
        <p class="card-description">Silakan masukan data barang baru</p>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success')}}
        </div>
        @endif

        <form class="forms-sample" action="{{ route('barang.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nama">Nama Barang</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Ketik nama barangnya di sini..." value="{{ old('nama') }}" required>

                @error('nama')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" placeholder="Berapa harganya?" value="{{ old('harga') }}" min="0" required>

                @error('harga')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-gradient-primary me-2">
                Simpan
            </button>
            <a href="{{ route('barang.index') }}" class="btn btn-light">
                Batal
            </a>
        </form>
    </div>
</div>
@endsection