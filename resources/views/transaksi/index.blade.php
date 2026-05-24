<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Barang - StockFlow SPPG</title>
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
        .action-card { border-radius: 10px; padding: 15px 20px; color: white; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: transform 0.2s; border: none; width: 100%; text-align: left; }
        .action-card:hover { transform: scale(1.02); }
        .card-in { background: linear-gradient(135deg, #198754, #28a745); } /* Hijau */
        .card-out { background: linear-gradient(135deg, #dc3545, #bd2130); } /* Merah */
        .data-card { background: white; border-radius: 10px; padding: 20px; margin-top: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
        .badge-tipe { font-size: 0.85rem; padding: 5px 10px; border-radius: 30px; }
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
            <a class="nav-link" href="{{ route('kategori.index') }}"><i class="bi bi-tags me-2"></i> Kategori Barang</a>
            <a class="nav-link" href="{{ route('barang.index') }}"><i class="bi bi-box me-2"></i> Daftar Barang</a>
            <a class="nav-link active" href="{{ route('transaksi.index') }}"><i class="bi bi-arrow-left-right me-2"></i> Transaksi</a>
             <a class="nav-link" href="{{ route('laporan.index') }}"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Stok</a>
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
                <h3 class="mb-1"><i class="bi bi-arrow-left-right"></i> Transaksi Barang</h3>
                <small class="text-secondary">Riwayat transaksi barang masuk dan keluar</small>
            </div>
            @if(Auth::user()->role !== 'kepala dapur')
            <div class="d-flex gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalMasuk"><i class="bi bi-plus-circle"></i> Barang Masuk</button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalKeluar"><i class="bi bi-dash-circle"></i> Barang Keluar</button>
            </div>
            @endif
        </div>

        @if(Auth::user()->role !== 'kepala dapur')
        <div class="row mt-3">
            <div class="col-md-6 mb-3">
                <button class="action-card card-in shadow-sm" data-bs-toggle="modal" data-bs-target="#modalMasuk">
                    <div>
                        <h5 class="mb-0">Quick In (Barang Masuk)</h5>
                        <small>Catat barang masuk dari supplier</small>
                    </div>
                    <div class="btn btn-light text-success"><i class="bi bi-box-arrow-in-right fs-5"></i></div>
                </button>
            </div>
            <div class="col-md-6 mb-3">
                <button class="action-card card-out shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKeluar">
                    <div>
                        <h5 class="mb-0">Quick Out (Barang Keluar)</h5>
                        <small>Catat pengeluaran barang untuk dapur/kebutuhan</small>
                    </div>
                    <div class="btn btn-light text-danger"><i class="bi bi-box-arrow-right fs-5"></i></div>
                </button>
            </div>
        </div>
        @endif

        <div class="data-card mt-2">
            <h6 class="mb-3"><i class="bi bi-clock-history"></i> Riwayat Transaksi <span class="badge bg-secondary">{{ $transaksis->count() }} Data</span></h6>
            
            @if($transaksis->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-arrow-left-right fs-1 text-secondary"></i>
                    <h5 class="mt-3">Tidak ada data transaksi</h5>
                    <p>Silakan catat transaksi masuk atau keluar menggunakan tombol di atas.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 12%">Tipe</th>
                                <th style="width: 23%">Barang</th>
                                <th style="width: 10%">Jumlah</th>
                                <th style="width: 15%">Tanggal</th>
                                <th style="width: 20%">Supplier / Detail</th>
                                <th style="width: 15%">Operator</th>
                                @if(Auth::user()->role !== 'kepala dapur')
                                    <th style="width: 5%" class="text-end">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksis as $index => $t)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($t->tipe == 'masuk')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle badge-tipe"><i class="bi bi-arrow-down-left"></i> Masuk</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle badge-tipe"><i class="bi bi-arrow-up-right"></i> Keluar</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $t->barang->nama_barang ?? 'Barang Terhapus' }}</td>
                                <td>
                                    @if($t->tipe == 'masuk')
                                        <span class="text-success fw-bold">+{{ $t->jumlah }}</span>
                                    @else
                                        <span class="text-danger fw-bold">-{{ $t->jumlah }}</span>
                                    @endif
                                    <small class="text-muted">{{ $t->barang->satuan ?? '' }}</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
                                <td>
                                    @if($t->tipe == 'masuk')
                                        <span class="text-secondary"><i class="bi bi-truck"></i> {{ $t->supplier->nama_supplier ?? 'Supplier Terhapus' }}</span>
                                    @else
                                        <span class="text-muted"><i class="bi bi-house-door"></i> Keperluan Dapur</span>
                                    @endif
                                </td>
                                <td>
                                    <small><i class="bi bi-person"></i> {{ $t->user->name ?? 'User' }}</small>
                                </td>
                                @if(Auth::user()->role !== 'kepala dapur')
                                <td class="text-end">
                                    @if($t->tipe == 'masuk')
                                        <form action="{{ route('transaksi.destroyMasuk', $t->id_transaksi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi masuk ini? Penghapusan akan mengembalikan stok.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-action text-danger border-0"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @else
                                        <form action="{{ route('transaksi.destroyKeluar', $t->id_transaksi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi keluar ini? Penghapusan akan mengembalikan stok.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-action text-danger border-0"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
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

    <!-- Modal Barang Masuk -->
    <div class="modal fade" id="modalMasuk" tabindex="-1" aria-labelledby="modalMasukLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title" id="modalMasukLabel"><i class="bi bi-box-arrow-in-right"></i> Catat Barang Masuk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('transaksi.storeMasuk') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_barang" class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                            <select name="id_barang" id="id_barang" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Barang --</option>
                                @foreach($barangs as $b)
                                    <option value="{{ $b->id_barang }}">{{ $b->nama_barang }} (Stok saat ini: {{ $b->stok }} {{ $b->satuan }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="id_supplier" class="form-label">Pilih Supplier <span class="text-danger">*</span></label>
                            <select name="id_supplier" id="id_supplier" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Supplier --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah Masuk <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan jumlah barang masuk" min="1" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_masuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="{{ date('Y-m-d') }}" required autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success text-white">Catat Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Barang Keluar -->
    <div class="modal fade" id="modalKeluar" tabindex="-1" aria-labelledby="modalKeluarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title" id="modalKeluarLabel"><i class="bi bi-box-arrow-right"></i> Catat Barang Keluar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('transaksi.storeKeluar') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_barang_keluar" class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                            <select name="id_barang" id="id_barang_keluar" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Barang --</option>
                                @foreach($barangs as $b)
                                    <option value="{{ $b->id_barang }}" data-stok="{{ $b->stok }}" data-nama="{{ $b->nama_barang }}" data-satuan="{{ $b->satuan }}" {{ $b->stok <= 0 ? 'disabled' : '' }}>
                                        {{ $b->nama_barang }} (Stok saat ini: {{ $b->stok }} {{ $b->satuan }}) {{ $b->stok <= 0 ? '-- HABIS --' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah_keluar" class="form-label">Jumlah Keluar <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah_keluar" name="jumlah" placeholder="Masukkan jumlah barang keluar" min="1" required autocomplete="off">
                            <div id="warning-stok" class="alert alert-danger mt-2 d-none shadow-sm py-2 px-3" style="font-size: 0.85rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Stok tidak mencukupi! Barang kurang.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_keluar" class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar" value="{{ date('Y-m-d') }}" required autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btn-submit-keluar" class="btn btn-danger text-white">Catat Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectBarang = document.getElementById('id_barang_keluar');
            const inputJumlah = document.getElementById('jumlah_keluar');
            const warningStok = document.getElementById('warning-stok');
            const btnSubmit = document.getElementById('btn-submit-keluar');

            function checkStock() {
                const selectedOption = selectBarang.options[selectBarang.selectedIndex];
                if (!selectedOption || selectBarang.value === '') {
                    warningStok.classList.add('d-none');
                    btnSubmit.disabled = false;
                    return;
                }

                const stokAvailable = parseInt(selectedOption.getAttribute('data-stok')) || 0;
                const namaBarang = selectedOption.getAttribute('data-nama') || '';
                const satuanBarang = selectedOption.getAttribute('data-satuan') || '';
                const jumlahKeluar = parseInt(inputJumlah.value) || 0;

                if (jumlahKeluar > stokAvailable) {
                    warningStok.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> Stok <strong>${namaBarang}</strong> tidak mencukupi! Hanya tersedia <strong>${stokAvailable} ${satuanBarang}</strong>.`;
                    warningStok.classList.remove('d-none');
                    btnSubmit.disabled = true;
                } else {
                    warningStok.classList.add('d-none');
                    btnSubmit.disabled = false;
                }
            }

            if (selectBarang && inputJumlah) {
                selectBarang.addEventListener('change', checkStock);
                inputJumlah.addEventListener('input', checkStock);
            }
        });
    </script>
</body>
</html>