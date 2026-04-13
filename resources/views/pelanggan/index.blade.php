@extends('pelanggan.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Daftar Data Customer</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Alamat Lengkap</th>
                        <th width="15%">Foto (Kamera)</th>
                        <th width="10%">Metode Simpan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggans as $index => $p)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><strong>{{ $p->nama }}</strong></td>
                            <td>
                                {{ $p->alamat }}<br>
                                <small class="text-muted">{{ $p->kecamatan }}, {{ $p->kota }}, {{ $p->provinsi }} ({{ $p->kodepos }})</small>
                            </td>
                            <td class="text-center">
                                @if($p->tipe_simpan == 'blob')
                                    <!-- Jika blob base64 -->
                                    <img src="{{ $p->foto }}" alt="Foto Customer" class="img-thumbnail" style="width:100px;height:100px;object-fit:cover;">
                                @else
                                    <!-- Jika fisik (path) -->
                                    <img src="{{ asset($p->foto) }}" alt="Foto Customer" class="img-thumbnail" style="width:100px;height:100px;object-fit:cover;">
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $p->tipe_simpan == 'blob' ? 'bg-info' : 'bg-success' }}">
                                    {{ strtoupper($p->tipe_simpan) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
