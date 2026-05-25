<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Supplier - StockFlow SPPG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; overflow-x: hidden; }
        .sidebar { height: 100vh; background-color: #1a1d2d; color: #fff; position: fixed; width: 250px; z-index: 100; }
        .sidebar-brand { padding: 20px; font-weight: bold; font-size: 1.2rem; }
        .sidebar-user { padding: 15px 20px; background-color: #24283b; margin-bottom: 20px; }
        .nav-link { color: #a9b1d6; padding: 12px 20px; }
        .nav-link:hover, .nav-link.active { background-color: #414868; color: #fff; border-radius: 5px; margin: 0 10px; }
        .main-content { margin-left: 250px; padding: 20px; min-height: 100vh; }
        .header-banner { background-color: #1a1d2d; color: white; padding: 20px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; }
        .data-card { background: white; border-radius: 10px; padding: 20px; margin-top: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
        .empty-state { text-align: center; padding: 50px 0; color: #6c757d; }
        .empty-icon { font-size: 4rem; margin-bottom: 15px; color: #ced4da; }
        .btn-action { font-size: 0.85rem; padding: 5px 10px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-box-seam"></i> StockFlow
            <div class="fs-6 fw-normal text-secondary">Sistem Kelola Barang</div>
        </div>
        
        <div class="sidebar-user">
            <div><i class="bi bi-person-circle"></i> {{ Auth::user()->name }}</div>
            <span class="badge bg-warning text-dark mt-1">{{ ucfirst(Auth::user()->role) }}</span>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a>
            <a class="nav-link" href="{{ route('kategori.index') }}"><i class="bi bi-tags me-2"></i> Kategori Barang</a>
            <a class="nav-link" href="{{ route('barang.index') }}"><i class="bi bi-box me-2"></i> Daftar Barang</a>
            <a class="nav-link" href="{{ route('transaksi.index') }}"><i class="bi bi-arrow-left-right me-2"></i> Transaksi</a>
            <a class="nav-link" href="{{ route('laporan.index') }}"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Stok</a>
            <a class="nav-link active" href="{{ route('supplier.index') }}"><i class="bi bi-truck me-2"></i> Supplier</a>
            @if(Auth::user()->role === 'admin')
                <a class="nav-link" href="{{ route('users.index') }}"><i class="bi bi-people me-2"></i> Kelola User</a>
            @endif
        </nav>

        <div class="position-absolute bottom-0 w-100 p-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100 text-start border-0"><i class="bi bi-box-arrow-left me-2"></i> Keluar Sistem</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <!-- Notifikasi Status -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Gagal memproses data:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="header-banner">
            <div>
                <h3 class="mb-1"><i class="bi bi-truck"></i> Daftar Supplier</h3>
                <small class="text-secondary">Kelola daftar penyedia/supplier bahan baku dapur</small>
            </div>
            @if(Auth::user()->role !== 'kepala dapur')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-circle"></i> Tambah Supplier</button>
            @endif
        </div>

        <div class="data-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="bi bi-truck"></i> Data Supplier <span class="badge bg-secondary">{{ $suppliers->count() }} Terdaftar</span></h6>
            </div>

            @if($suppliers->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-truck empty-icon"></i>
                    <h5>Belum ada data supplier</h5>
                    <p>Silakan daftarkan supplier untuk mencatat transaksi barang masuk.</p>
                    @if(Auth::user()->role !== 'kepala dapur')
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-circle"></i> Tambah Supplier Pertama</button>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 25%">Nama Supplier</th>
                                <th style="width: 40%">Alamat</th>
                                <th style="width: 15%" class="text-center">Jumlah Transaksi Masuk</th>
                                @if(Auth::user()->role !== 'kepala dapur')
                                    <th style="width: 15%" class="text-end">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $index => $s)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold text-dark">{{ $s->nama_supplier }}</td>
                                <td>{{ $s->alamat ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-3 py-2 fw-normal">
                                        <i class="bi bi-arrow-down-left-square text-success me-1"></i> {{ $s->barang_masuks_count }} Transaksi
                                    </span>
                                </td>
                                @if(Auth::user()->role !== 'kepala dapur')
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-warning btn-action text-white btn-edit" 
                                                data-id="{{ $s->id_supplier }}"
                                                data-nama="{{ $s->nama_supplier }}"
                                                data-alamat="{{ $s->alamat }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('supplier.destroy', $s->id_supplier) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-action text-white"><i class="bi bi-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if(Auth::user()->role !== 'kepala dapur')
    <!-- Modal Tambah Supplier -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title" id="modalTambahLabel"><i class="bi bi-plus-circle"></i> Tambah Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('supplier.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_supplier" class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" placeholder="Contoh: PT. Sumber Protein, Toko Sayur Sehat" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Supplier</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap supplier (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Supplier -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-white border-0">
                    <h5 class="modal-title" id="modalEditLabel"><i class="bi bi-pencil-square"></i> Edit Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditSupplier" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nama_supplier" class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_supplier" name="nama_supplier" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="edit_alamat" class="form-label">Alamat Supplier</label>
                            <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-white">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @if(Auth::user()->role !== 'kepala dapur')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.btn-edit');
            const formEdit = document.getElementById('formEditSupplier');
            const inputNama = document.getElementById('edit_nama_supplier');
            const inputAlamat = document.getElementById('edit_alamat');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');
                    const alamat = this.getAttribute('data-alamat');

                    // Set action URL pada form secara dinamis
                    formEdit.setAttribute('action', `/supplier/${id}`);
                    // Isi data
                    inputNama.value = nama;
                    inputAlamat.value = alamat;
                });
            });
        });
    </script>
    @endif
</body>
</html>
