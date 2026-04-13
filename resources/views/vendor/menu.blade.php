<!DOCTYPE html>
<html lang="id">
<head>
    <title>Menu - {{ $vendor->nama_vendor }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tambahan Bootstrap Bundle untuk Modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light p-5">
    <div class="container">
        <a href="{{ route('vendor.index') }}" class="btn btn-secondary mb-3">&laquo; Kembali</a>
        <h2>Master Menu - {{ $vendor->nama_vendor }}</h2>
        <hr>

        <div class="row">
            <div class="col-md-5">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">Tambah Menu Baru</div>
                    <div class="card-body">
                        <form id="formTambahMenu">
                            <div class="mb-3">
                                <label>Nama Menu</label>
                                <input type="text" name="nama_menu" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Upload Gambar Menu</label>
                                <input type="file" name="gambar" accept="image/*" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Simpan Menu</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <h5>Daftar Menu Saya</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped bg-white" id="tabelMenu">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Menu</th>
                                <th>Harga</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $m)
                            <tr>
                                <td class="text-center">
                                    @if($m->path_gambar)
                                        <img src="{{ asset('storage/' . $m->path_gambar) }}" width="60" alt="Gambar">
                                    @else 
                                        <i>Tanpa Gambar</i> 
                                    @endif
                                </td>
                                <td>{{ $m->nama_menu }}</td>
                                <td>Rp {{ number_format($m->harga, 0, ',', '.') }}</td>
                                <td>
                                    <!-- Tombol Edit & Hapus -->
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-warning btn-sm btn-edit text-white" 
                                            data-id="{{ $m->id }}" 
                                            data-nama="{{ $m->nama_menu }}" 
                                            data-harga="{{ $m->harga }}">Edit</button>
                                        <button class="btn btn-danger btn-sm btn-hapus" 
                                            data-id="{{ $m->id }}">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada menu yang dibuat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Menu -->
    <div class="modal fade" id="modalEditMenu" tabindex="-1" aria-labelledby="modalEditMenuLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditMenu">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="modalEditMenuLabel">Edit Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Disembunyikan karena ID hanya untuk route param -->
                        <input type="hidden" id="edit_idmenu">
                        <div class="mb-3">
                            <label>Nama Menu</label>
                            <input type="text" id="edit_nama_menu" name="nama_menu" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Harga (Rp)</label>
                            <input type="number" id="edit_harga" name="harga" class="form-control" required>
                        </div>
                        <div class="alert alert-info py-2">
                            <small>Kosongkan file di bawah ini jika tidak ingin merubah gambar menu.</small>
                        </div>
                        <div class="mb-3">
                            <label>Ganti Gambar Menu (Opsional)</label>
                            <input type="file" name="gambar" accept="image/*" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script AJAX Submit Form -->
    <script>
        // Set CSRF default untuk semua requet HTTP jQuery
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 1. TAMBAH MENU
        $('#formTambahMenu').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            
            $.ajax({
                url: "{{ route('vendor.menu.tambah', $vendor->id) }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response.code === 200) {
                        Swal.fire('Sukses', response.message, 'success').then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(err) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengupload menu. Pastikan ukuran gambar < 2MB', 'error');
                }
            });
        });

        // 2. BUKA MODAL EDIT MENU & ISI DATA LAMA
        const editModal = new bootstrap.Modal(document.getElementById('modalEditMenu'));
        
        $('.btn-edit').on('click', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let harga = $(this).data('harga');

            // Set the modal forms value 
            $('#edit_idmenu').val(id);
            $('#edit_nama_menu').val(nama);
            $('#edit_harga').val(harga);

            editModal.show();
        });

        // 3. SUBMIT EDIT MENU
        $('#formEditMenu').on('submit', function(e) {
            e.preventDefault();
            let idmenu = $('#edit_idmenu').val();
            let formData = new FormData(this); // Berjalan dengan HTTP POST (form multipart upload)
            
            $.ajax({
                url: `/vendor/{{ $vendor->id }}/menu/${idmenu}/update`,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response.code === 200) {
                        editModal.hide();
                        Swal.fire('Terupdate', response.message, 'success').then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(err) {
                    Swal.fire('Gagal', 'Gagal update data menu.', 'error');
                }
            });
        });

        // 4. HAPUS MENU
        $('.btn-hapus').on('click', function() {
            let idmenu = $(this).data('id');
            
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Menu terpilih akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/vendor/{{ $vendor->id }}/menu/${idmenu}/hapus`,
                        type: "DELETE",
                        success: function(response) {
                            if(response.code === 200) {
                                Swal.fire('Terhapus!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(err) {
                            Swal.fire('Gagal', 'Gagal menghapus data menu mungkin karena menu sudah terhubung ke riwayat pesanan.', 'error');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>