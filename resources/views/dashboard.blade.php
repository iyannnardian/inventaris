<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StockFlow SPPG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; overflow-x: hidden; }
        
        /* Desain Sidebar */
        .sidebar { height: 100vh; background-color: #1a1d2d; color: #fff; position: fixed; width: 250px; z-index: 100; }
        .sidebar-brand { padding: 20px; font-weight: bold; font-size: 1.2rem; }
        .sidebar-user { padding: 15px 20px; background-color: #24283b; margin-bottom: 20px; }
        .nav-link { color: #a9b1d6; padding: 12px 20px; }
        .nav-link:hover, .nav-link.active { background-color: #414868; color: #fff; border-radius: 5px; margin: 0 10px; }
        
        /* Area Konten Utama */
        .main-content { margin-left: 250px; padding: 20px; }
        
        /* Banner Atas (Header Gelap) */
        .top-banner { 
            background-color: #1a1d2d; 
            color: white; 
            padding: 30px 25px; 
            border-radius: 10px; 
            margin-bottom: 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        
        /* Kartu Statistik Ungu */
        .stat-card { 
            background-color: #462d44; /* Warna ungu gelap sesuai gambar */
            color: white; 
            border-radius: 12px; 
            padding: 20px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            height: 100%;
        }
        .stat-card h6 { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #d8b4e2; margin-bottom: 10px; }
        .stat-card h2 { font-size: 2.5rem; margin: 0; font-weight: bold; }
        .stat-card small { font-size: 0.8rem; color: #d8b4e2; }
        
        /* Kartu Grafik Bawah */
        .chart-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); height: 100%; }
        .dummy-chart-area { height: 250px; border: 2px dashed #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; background-color: #f8fafc; }
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
            <a class="nav-link active" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a>
            <a class="nav-link" href="{{ route('kategori.index') }}"><i class="bi bi-tags me-2"></i> Kategori Barang</a>
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
                <button type="submit" class="btn btn-outline-light w-100 text-start border-0"><i class="bi bi-box-arrow-left me-2"></i> Keluar Sistem</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        
        <div class="top-banner">
            <div>
                <h2 class="fw-bold mb-1">Sistem Stok Barang</h2>
                <p class="text-secondary mb-0">Selamat datang {{ Auth::user()->name }}</p>
            </div>
            <div class="text-end bg-dark p-2 px-3 rounded text-white" style="background-color: rgba(0,0,0,0.3) !important;">
                <small class="text-secondary" id="tgl-hari-ini">Memuat tanggal...</small><br>
                <strong class="fs-5" id="jam-hari-ini">00:00 WIB</strong>
            </div>
        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-md-4">
                <div class="stat-card">
                    <h6>TOTAL BARANG</h6>
                    <h2>{{ $totalBarang }}</h2>
                    <small>Item terdaftar</small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card">
                    <h6>BARANG MASUK</h6>
                    <h2>{{ $totalMasuk }}</h2>
                    <small>Total transaksi masuk</small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card">
                    <h6>BARANG KELUAR</h6>
                    <h2>{{ $totalKeluar }}</h2>
                    <small>Total transaksi keluar</small>
                </div>
            </div>
            
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="chart-card">
                    <h5 class="mb-0">Grafik Stok Bulanan</h5>
                    <small class="text-muted">Tren pergerakan barang masuk & keluar</small>
                    <div class="dummy-chart-area mt-3 text-muted">
                        <i class="bi bi-graph-up text-secondary me-2"></i> Area Grafik Garis Akan Ditampilkan Di Sini
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-card">
                    <h5 class="mb-0">Distribusi Kategori</h5>
                    <small class="text-muted">Persentase per kategori</small>
                    <div class="dummy-chart-area mt-3 text-muted">
                        <i class="bi bi-pie-chart text-secondary me-2"></i> Area Grafik Lingkaran Di Sini
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('tgl-hari-ini').innerText = now.toLocaleDateString('id-ID', options);
            document.getElementById('jam-hari-ini').innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>
</html>