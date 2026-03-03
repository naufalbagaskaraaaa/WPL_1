<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="p-4">

    <div class="container">
        <h2 class="mb-4">📦 Data Barang UMKM</h2>

        {{-- Notifikasi sukses --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- ✅ Form cetak — membungkus tabel agar checkbox ikut terkirim --}}
        <form action="{{ route('barang.cetak') }}" method="POST" id="form-cetak">
            @csrf

            {{-- Error validasi --}}
            @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            {{-- Tombol aksi atas --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('barang.create') }}" class="btn btn-primary">+ Tambah Barang</a>

                {{-- Tombol cetak hanya muncul kalau ada yang dicentang --}}
                <div id="panel-cetak" class="d-none">
                    <span id="jumlah-dipilih" class="me-3 text-success fw-bold"></span>

                    {{-- Input koordinat X & Y --}}
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

                    {{-- ✅ [6c] Tombol Preview — ditambahkan sebelum tombol Cetak PDF --}}
                    <button type="button" class="btn btn-outline-secondary me-2" id="btn-preview">
                        👁️ Preview
                    </button>

                    <button type="submit" class="btn btn-success">
                        🖨️ Cetak PDF
                    </button>
                </div>
            </div>

            {{-- Tabel --}}
            <table id="tabel-barang" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        {{-- Checkbox pilih semua --}}
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

    {{-- ✅ [6c] Modal Preview Grid --}}
    <div class="modal fade" id="modal-preview" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📍 Preview Posisi Cetak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2 text-muted">
                        Kotak <span class="badge bg-danger">merah</span> = posisi mulai cetak.
                        Kotak <span class="badge bg-secondary">abu</span> = label sudah terpakai (dilewati).
                    </p>

                    {{-- Grid 5x8 preview --}}
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {

            // ✅ Inisialisasi DataTables
            var table = $('#tabel-barang').DataTable({
                processing: true,
                serverSide: false,
                ajax: '{{ route("barang.index") }}',

                columns: [{
                        // Kolom checkbox — render manual
                        data: 'id_barang',
                        orderable: false,
                        searchable: false,
                        render: function(id) {
                            return '<input type="checkbox" class="check-item" name="selected[]" value="' + id + '">';
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

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Selanjutnya"
                    },
                    zeroRecords: "Data tidak ditemukan",
                    processing: "Memuat data..."
                }
            });

            // ✅ Checkbox "Pilih Semua"
            $('#check-all').on('change', function() {
                var checked = $(this).is(':checked');
                // Centang/uncentang semua checkbox yang tampil di halaman ini
                table.rows({
                        page: 'current'
                    }).nodes().to$()
                    .find('.check-item').prop('checked', checked);
                updatePanelCetak();
            });

            // ✅ Update panel cetak saat checkbox berubah
            $('#tabel-barang').on('change', '.check-item', function() {
                updatePanelCetak();
            });

            function updatePanelCetak() {
                var jumlah = $('.check-item:checked').length;

                if (jumlah > 0) {
                    // Tampilkan panel cetak
                    $('#panel-cetak').removeClass('d-none');
                    $('#jumlah-dipilih').text(jumlah + ' data dipilih');
                } else {
                    // Sembunyikan panel cetak
                    $('#panel-cetak').addClass('d-none');
                }
            }

            // ✅ [6c] Update preview grid saat nilai X atau Y berubah
            $(document).on('input', '[name="koordinat_x"], [name="koordinat_y"]', function() {
                updatePreviewGrid();
            });

            // ✅ [6c] Tampilkan modal preview saat klik tombol preview
            $(document).on('click', '#btn-preview', function() {
                updatePreviewGrid();
                var modal = new bootstrap.Modal(document.getElementById('modal-preview'));
                modal.show();
            });

            // ✅ [6c] Fungsi update warna grid sesuai koordinat X & Y
            function updatePreviewGrid() {
                var x = parseInt($('[name="koordinat_x"]').val()) || 1;
                var y = parseInt($('[name="koordinat_y"]').val()) || 1;

                // Hitung offset — berapa slot yang dilewati
                var offset = (y - 1) * 5 + (x - 1);

                // Reset semua sel
                $('#preview-grid td').css('background-color', '').css('color', '');

                // Warnai slot yang dilewati (abu-abu) & slot mulai cetak (merah)
                var count = 0;
                $('#preview-grid td').each(function() {
                    if (count < offset) {
                        $(this).css('background-color', '#dee2e6').css('color', '#999');
                    } else if (count === offset) {
                        // Warnai slot mulai cetak (merah)
                        $(this).css('background-color', '#dc3545').css('color', 'white');
                        return false; // stop loop
                    }
                    count++;
                });
            }

        });
    </script>

</body>

</html>