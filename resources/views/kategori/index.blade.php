<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Barang - StockFlow SPPG</title>
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
        .category-card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s; border: 1px solid rgba(0,0,0,0.05); }
        .category-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .btn-action { font-size: 0.85rem; padding: 5px 10px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="bi bi-box-seam"></i> StockFlow</div>
        <div class="sidebar-user">
            <div><i class="bi bi-person-circle"></i> {{ Auth::user()->name }}</div>
            <span class="badge bg-warning text-dark mt-1">{{ ucfirst(Auth::user()->role) }}</span>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a>
            <a class="nav-link active" href="{{ route('kategori.index') }}"><i class="bi bi-tags me-2"></i> Kategori Barang</a>
            <a class="nav-link" href="{{ route('barang.index') }}"><i class="bi bi-box me-2"></i> Daftar Barang</a>
            <a class="nav-link" href="{{ route('transaksi.index') }}"><i class="bi bi-arrow-left-right me-2"></i> Transaksi</a>
            <a class="nav-link" href="{{ route('laporan.index') }}"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Stok</a>
            <a class="nav-link" href="{{ route('supplier.index') }}"><i class="bi bi-truck me-2"></i> Supplier</a>
            @if(Auth::user()->role === 'admin')
                <a class="nav-link" href="{{ route('users.index') }}"><i class="bi bi-people me-2"></i> Kelola User</a>
            @endif
        </nav>
        <div class="position-absolute bottom-0 w-100 p-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100 text-start border-0"><i class="bi bi-box-arrow-left me-2"></i> Keluar</button>
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
                <h3 class="mb-1"><i class="bi bi-tags"></i> Kategori Barang</h3>
                <small class="text-secondary">Kelola kategori untuk pengelompokan barang</small>
            </div>
            @if(Auth::user()->role !== 'kepala dapur')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-circle"></i> Tambah Kategori</button>
            @endif
        </div>

        <div class="row mt-4">
            @forelse($kategoris as $k)
            <div class="col-md-4">
                <div class="category-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-0 text-truncate" style="max-width: 70%;">{{ $k->nama_kategori }}</h5>
                        <span class="badge bg-primary">{{ $k->barangs_count }} Items</span>
                    </div>
                    <p class="text-muted small mb-3">Dibuat pada {{ $k->created_at->format('d M Y') }}</p>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-2">
                        <button class="btn btn-outline-primary btn-action btn-view-items" 
                                data-id="{{ $k->id_kategori }}" 
                                data-nama="{{ $k->nama_kategori }}"
                                data-barangs="{{ $k->barangs->toJson() }}"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalViewBarang">
                            <i class="bi bi-eye"></i> Lihat Barang
                        </button>
                        
                        @if(Auth::user()->role !== 'kepala dapur')
                        <div class="d-flex gap-2">
                            <button class="btn btn-warning btn-action text-white btn-edit" 
                                    data-id="{{ $k->id_kategori }}" 
                                    data-nama="{{ $k->nama_kategori }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEdit"
                                    title="Edit Kategori">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('kategori.destroy', $k->id_kategori) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-action text-white" title="Hapus Kategori"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-tags fs-1"></i>
                <p class="mt-2">Belum ada kategori yang ditambahkan.</p>
                @if(Auth::user()->role !== 'kepala dapur')
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-circle"></i> Tambah Kategori Pertama</button>
                @endif
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title" id="modalTambahLabel"><i class="bi bi-plus-circle"></i> Tambah Kategori</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kategori.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" placeholder="Contoh: Bahan Pokok, Sayuran, Daging" required autocomplete="off">
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

    <!-- Modal Edit Kategori -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-white border-0">
                    <h5 class="modal-title" id="modalEditLabel"><i class="bi bi-pencil-square"></i> Edit Kategori</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditKategori" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required autocomplete="off">
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

    <!-- Modal Lihat Barang di Kategori -->
    <div class="modal fade" id="modalViewBarang" tabindex="-1" aria-labelledby="modalViewBarangLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title" id="modalViewBarangLabel"><i class="bi bi-box"></i> Daftar Barang Kategori: <span id="view_nama_kategori" class="fw-bold"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="table-kategori-barang">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 10%">No</th>
                                    <th style="width: 60%">Nama Barang</th>
                                    <th style="width: 30%">Stok Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody id="list-barang-kategori">
                                <!-- Isi dinamis lewat JS -->
                            </tbody>
                        </table>
                    </div>
                    <div id="empty-barang-state" class="text-center py-4 d-none text-muted">
                        <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
                        <p class="mb-0">Belum ada barang yang didaftarkan pada kategori ini.</p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logika untuk mengisi data pada Modal Edit Kategori secara dinamis
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.btn-edit');
            const formEdit = document.getElementById('formEditKategori');
            const inputNama = document.getElementById('edit_nama_kategori');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');

                    // Set action URL pada form secara dinamis
                    formEditKategori.setAttribute('action', `/kategori/${id}`);
                    // Isi nama kategori
                    inputNama.value = nama;
                });
            });

            // Logika untuk menampilkan daftar barang dalam Kategori dinamis
            const viewBarangButtons = document.querySelectorAll('.btn-view-items');
            const viewNamaKategori = document.getElementById('view_nama_kategori');
            const listBarangKategori = document.getElementById('list-barang-kategori');
            const tableKategoriBarang = document.getElementById('table-kategori-barang');
            const emptyBarangState = document.getElementById('empty-barang-state');

            viewBarangButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const nama = this.getAttribute('data-nama');
                    const barangsJson = this.getAttribute('data-barangs');
                    const barangs = JSON.parse(barangsJson) || [];

                    viewNamaKategori.textContent = nama;
                    listBarangKategori.innerHTML = '';

                    if (barangs.length === 0) {
                        tableKategoriBarang.classList.add('d-none');
                        emptyBarangState.classList.remove('d-none');
                    } else {
                        tableKategoriBarang.classList.remove('d-none');
                        emptyBarangState.classList.add('d-none');

                        barangs.forEach((b, index) => {
                            const row = document.createElement('tr');
                            const stokBadge = b.stok <= 5
                                ? `<span class="badge bg-danger py-2 px-3"><i class="bi bi-exclamation-triangle-fill"></i> ${b.stok} ${b.satuan} (Kritis)</span>`
                                : `<span class="badge bg-success py-2 px-3"><i class="bi bi-check-circle-fill"></i> ${b.stok} ${b.satuan}</span>`;

                            row.innerHTML = `
                                <td>${index + 1}</td>
                                <td class="fw-semibold text-dark">${b.nama_barang}</td>
                                <td>${stokBadge}</td>
                            `;
                            listBarangKategori.appendChild(row);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>