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

                    <form class="forms-sample" action="{{ route('buku.store') }}" method="POST">
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
                        <label for="kode">
                            Kode Buku
                        </label>
                        <input type="text" class="form-control" id="kode" name="kode" placeholder="kode bukunya jangan lupa!!"required>
                      </div>

                      <div class="form-group">
                        <label for="judul">
                            Judul Buku
                        </label>
                        <input type="text" class="form-control" id="judul" name="judul" placeholder="judulnya apa sayang?"required>
                      </div>

                      <div class="form-group">
                        <label for="pengarang">
                            pengarang
                        </label>
                        <input type="text" class="form-control" id="pengarang" name="pengarang" placeholder="ini yang buat kamu bukan sih?"required>
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