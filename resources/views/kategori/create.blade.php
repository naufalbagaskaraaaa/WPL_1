@extends('layouts.main')
@section('content')
<div class="card">
                  <div class="card-body">
                    <h4 class="card-title">
                        Tambah Kategori
                    </h4>
                    <p class="card-description">
                        Silakan masukan kategori buku
                    </p>

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success')}}
                    </div>
                    @endif

                    <form class="forms-sample" action="{{ route('kategori.store') }}" method="POST">
                        @csrf

                      <div class="form-group">
                        <label for="nama_kategori">
                            Nama Kategori
                        </label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" placeholder="apa kategori buku itu?" required>
                      </div>
                      
                      <button type="submit" class="btn btn-gradient-primary me-2">
                        Simpan
                    </button>
                      <a href="{{ route('dashboard') }}" class="btn btn-light">
                        batal
                    </a>
                    </form>
                  </div>
                </div>
                @endsection