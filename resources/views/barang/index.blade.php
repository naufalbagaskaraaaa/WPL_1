@extends('layouts.main')

@section('style-page')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Data Barang</h4>
        <p class="card-description">Daftar seluruh data barang UMKM</p>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('barang.cetak') }}" method="POST" id="form-cetak" target="_blank">
            @csrf

            @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            <div id="notif-cetak" class="alert alert-success d-none">
                PDF berhasil dibuat! Silakan cek tab baru untuk mencetak.
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('barang.create') }}" class="btn btn-gradient-primary">+ Tambah Barang</a>

                <div id="panel-cetak" class="d-none">
                    <span id="jumlah-dipilih" class="me-3 text-success fw-bold"></span>

                    <div class="d-inline-flex align-items-center gap-2 me-2">
                        <label class="mb-0 fw-bold">Mulai dari:</label>

                        <div class="input-group" style="width:130px">
                            <span class="input-group-text">X</span>
                            <input type="number" name="koordinat_x" class="form-control"
                                value="1" min="1" max="5" required>
                        </div>

                        <div class="input-group" style="width:130px">
                            <span class="input-group-text">Y</span>
                            <input type="number" name="koordinat_y" class="form-control"
                                value="1" min="1" max="8" required>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-secondary me-2" id="btn-preview">
                        Preview
                    </button>

                    <button type="submit" class="btn btn-gradient-success">
                        Cetak PDF
                    </button>
                </div>
            </div>

            <table id="tabel-barang" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th style="width:40px">
                            <input type="checkbox" id="check-all" title="Pilih Semua">
                        </th>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </form>
    </div>
</div>

<div class="modal fade" id="modal-preview" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Posisi Cetak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2 text-muted">
                    Kotak <span class="badge bg-danger">merah</span> = posisi mulai cetak.
                </p>

                <table class="table table-bordered text-center" id="preview-grid"
                    style="table-layout:fixed; font-size:11px">
                    <tbody>
                        @for($y = 1; $y <= 8; $y++)
                            <tr>
                            @for($x = 1; $x <= 5; $x++)
                                <td style="height:35px; padding:2px"
                                data-x="{{ $x }}" data-y="{{ $y }}">
                                {{ $x }},{{ $y }}
                                </td>
                                @endfor
                                </tr>
                                @endfor
                    </tbody>
                </table>
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
        
     console.log('Form cetak ditemukan:', $('#form-cetak').length);

        var table = $('#tabel-barang').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{{ route("barang.index") }}',
            columns: [{
                    data: 'id_barang',
                    orderable: false,
                    searchable: false,
                    render: function(id) {
                        return '<input type="checkbox" class="check-item" value="' + id + '">';
                    }
                },
                {
                    data: 'id_barang',
                    name: 'id_barang'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'harga_format',
                    name: 'harga_format',
                    orderable: false
                },
                {
                    data: 'timestamp',
                    name: 'timestamp'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                },
            ],
        });

        $('#check-all').on('change', function() {
            var checked = $(this).is(':checked');
            table.rows({
                    page: 'current'
                }).nodes().to$()
                .find('.check-item').prop('checked', checked);
            updatePanelCetak();
        });

        $('#tabel-barang').on('change', '.check-item', function() {
            updatePanelCetak();
        });

        function updatePanelCetak() {
            var jumlah = $('.check-item:checked').length;
            if (jumlah > 0) {
                $('#panel-cetak').removeClass('d-none');
                $('#jumlah-dipilih').text(jumlah + ' data dipilih');
            } else {
                $('#panel-cetak').addClass('d-none');
            }
        }

        $('#form-cetak').on('submit', function(e) {
            console.log('Submit terpanggil!');
            e.preventDefault();

            $(this).find('input[name="selected_ids[]"]').remove();

            var dipilih = $('.check-item:checked');

            if (dipilih.length === 0) {
                alert('Pilih minimal 1 data untuk dicetak!');
                return;
            }

            dipilih.each(function() {
                $('#form-cetak').append(
                    '<input type="hidden" name="selected_ids[]" value="' + $(this).val() + '">'
                );
            });

            this.submit();

            $('#notif-cetak').removeClass('d-none');
            $('.check-item').prop('checked', false);
            $('#check-all').prop('checked', false);
            updatePanelCetak();
        });

        $(document).on('input', '[name="koordinat_x"], [name="koordinat_y"]', function() {
            updatePreviewGrid();
        });

        $(document).on('click', '#btn-preview', function() {
            updatePreviewGrid();
            var modal = new bootstrap.Modal(document.getElementById('modal-preview'));
            modal.show();
        });

        function updatePreviewGrid() {
            var x = parseInt($('[name="koordinat_x"]').val()) || 1;
            var y = parseInt($('[name="koordinat_y"]').val()) || 1;
            var offset = (y - 1) * 5 + (x - 1);

            $('#preview-grid td').css('background-color', '').css('color', '');

            var count = 0;
            $('#preview-grid td').each(function() {
                if (count === offset) {
                    $(this).css('background-color', '#dc3545').css('color', 'white');
                    return false;
                }
                count++;
            });
        }

    });
</script>
@endsection