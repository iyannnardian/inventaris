<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang - StockFlow SPPG</title>
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
        .filter-card, .data-card { background: white; border-radius: 10px; padding: 20px; margin-top: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
        .empty-state { text-align: center; padding: 50px 0; color: #6c757d; }
        .empty-icon { font-size: 4rem; margin-bottom: 15px; color: #ced4da; }
        .btn-action { font-size: 0.85rem; padding: 5px 10px; }
        .badge-stok { font-size: 0.9rem; padding: 6px 12px; border-radius: 30px; }
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
            <a class="nav-link active" href="{{ route('barang.index') }}"><i class="bi bi-box me-2"></i> Daftar Barang</a>
            <a class="nav-link" href="{{ route('transaksi.index') }}"><i class="bi bi-arrow-left-right me-2"></i> Transaksi</a>
            <a class="nav-link" href="{{ route('laporan.index') }}"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Stok</a>
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
                <h3 class="mb-1"><i class="bi bi-box"></i> Daftar Barang</h3>
                <small class="text-secondary">Kelola Data Barang dan Stok Barang</small>
            </div>
            @if(Auth::user()->role !== 'kepala dapur')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-circle"></i> Tambah Barang</button>
            @endif
        </div>

        <div class="filter-card">
            <form action="{{ route('barang.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label text-muted"><i class="bi bi-search"></i> Cari Barang</label>
                    <input type="text" name="search" class="form-control" placeholder="Masukkan nama barang..." value="{{ request('search') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label text-muted"><i class="bi bi-tag"></i> Kategori</label>
                    <select name="id_kategori" class="form-select">
                        <option value="all" selected>Semua Kategori</option>
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id_kategori }}" {{ request('id_kategori') == $k->id_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-light border w-100 text-center">Reset</a>
                </div>
            </form>
        </div>

        <div class="data-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="bi bi-calendar3"></i> Data Barang <span class="badge bg-secondary">{{ $barangs->count() }} Item</span></h6>
            </div>

            @if($barangs->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-box-seam empty-icon"></i>
                    <h5>Tidak ada data barang</h5>
                    <p>Belum ada barang yang ditemukan atau ditambahkan ke sistem.</p>
                    @if(Auth::user()->role !== 'kepala dapur')
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-circle"></i> Tambah Barang Pertama</button>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 25%">Nama Barang</th>
                                <th style="width: 20%">Kategori</th>
                                <th style="width: 15%">Satuan</th>
                                <th style="width: 15%">Stok Saat Ini</th>
                                @if(Auth::user()->role !== 'kepala dapur')
                                    <th style="width: 20%" class="text-end">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangs as $index => $b)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $b->nama_barang }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $b->kategori->nama_kategori ?? 'Tanpa Kategori' }}</span>
                                </td>
                                <td>{{ $b->satuan }}</td>
                                <td>
                                    @if($b->stok <= 5)
                                        <span class="badge bg-danger badge-stok"><i class="bi bi-exclamation-triangle-fill"></i> {{ $b->stok }} (Kritis)</span>
                                    @else
                                        <span class="badge bg-success badge-stok"><i class="bi bi-check-circle-fill"></i> {{ $b->stok }}</span>
                                    @endif
                                </td>
                                @if(Auth::user()->role !== 'kepala dapur')
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-warning btn-action text-white btn-edit" 
                                                data-id="{{ $b->id_barang }}"
                                                data-nama="{{ $b->nama_barang }}"
                                                data-kategori="{{ $b->id_kategori }}"
                                                data-satuan="{{ $b->satuan }}"
                                                data-stok-awal="{{ $b->stok_awal }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('barang.destroy', $b->id_barang) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
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

    <!-- Modal Tambah Barang -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title" id="modalTambahLabel"><i class="bi bi-plus-circle"></i> Tambah Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" placeholder="Contoh: Beras Pandan Wangi, Minyak Goreng" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="id_kategori" id="id_kategori" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                @foreach($kategoris as $k)
                                    <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan" id="satuan" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Satuan --</option>
                                <option value="Kg">Kg</option>
                                <option value="Ikat">Ikat</option>
                                <option value="Pcs">Pcs</option>
                                <option value="Botol">Botol</option>
                                <option value="Liter">Liter</option>
                                <option value="Butir">Butir</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="stok_awal" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stok_awal" name="stok_awal" placeholder="Masukkan jumlah stok awal (misal: 0)" min="0" required autocomplete="off">
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

    <!-- Modal Edit Barang -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-white border-0">
                    <h5 class="modal-title" id="modalEditLabel"><i class="bi bi-pencil-square"></i> Edit Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditBarang" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="edit_id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="id_kategori" id="edit_id_kategori" class="form-select" required>
                                @foreach($kategoris as $k)
                                    <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan" id="edit_satuan" class="form-select" required>
                                <option value="Kg">Kg</option>
                                <option value="Ikat">Ikat</option>
                                <option value="Pcs">Pcs</option>
                                <option value="Botol">Botol</option>
                                <option value="Liter">Liter</option>
                                <option value="Butir">Butir</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_stok_awal" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_stok_awal" name="stok_awal" min="0" required autocomplete="off">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.btn-edit');
            const formEdit = document.getElementById('formEditBarang');
            const inputNama = document.getElementById('edit_nama_barang');
            const selectKategori = document.getElementById('edit_id_kategori');
            const inputSatuan = document.getElementById('edit_satuan');
            const inputStokAwal = document.getElementById('edit_stok_awal');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');
                    const kategori = this.getAttribute('data-kategori');
                    const satuan = this.getAttribute('data-satuan');
                    const stokAwal = this.getAttribute('data-stok-awal');

                    formEdit.setAttribute('action', `/barang/${id}`);
                    inputNama.value = nama;
                    selectKategori.value = kategori;
                    
                    // Pencocokan case-insensitive untuk dropdown Satuan
                    const options = Array.from(inputSatuan.options);
                    const matchedOption = options.find(opt => opt.value.toLowerCase() === (satuan || '').toLowerCase());
                    if (matchedOption) {
                        inputSatuan.value = matchedOption.value;
                    } else {
                        inputSatuan.value = satuan;
                    }

                    inputStokAwal.value = stokAwal;
                });
            });
        });
    </script>
</body>
</html>