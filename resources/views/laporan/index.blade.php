<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Barang - StockFlow SPPG</title>
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
        .filter-card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
        .excel-sheet { background: white; border-radius: 10px; padding: 40px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.05); }
        
        /* Excel Table Styling */
        .excel-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: 'Arial', sans-serif; font-size: 0.9rem; }
        .excel-table th { background-color: #4f81bd; color: white; border: 1px solid #385d8a; padding: 8px 12px; font-weight: bold; text-align: center; }
        .excel-table td { border: 1px solid #bfbfbf; padding: 8px 12px; }
        .excel-table tr.category-header { background-color: #d9e1f2; font-weight: bold; }
        .excel-table tr.category-header td { border-bottom: 2px solid #8faadc; }
        
        /* Kop Laporan */
        .kop-laporan { text-align: center; margin-bottom: 30px; position: relative; }
        .kop-laporan h3 { font-weight: 800; font-size: 1.4rem; color: #1f4e78; margin-bottom: 2px; text-transform: uppercase; }
        .kop-laporan h4 { font-weight: 700; font-size: 1.15rem; color: #111; margin-bottom: 5px; text-transform: uppercase; }
        .kop-laporan p { color: #595959; font-size: 0.85rem; margin-bottom: 0; font-weight: 600; }
        
        .kop-actions { position: absolute; right: 0; top: 0; display: flex; gap: 8px; }
        .kop-btn { border-radius: 5px; padding: 6px 12px; font-size: 0.8rem; font-weight: bold; display: flex; align-items: center; justify-content: center; text-decoration: none; border: 1px solid #bfbfbf; }
        
        @media print {
            .sidebar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            .filter-card, .btn-actions-main { display: none !important; }
            .excel-sheet { border: none !important; box-shadow: none !important; padding: 0 !important; }
            .kop-actions { display: none !important; }
        }
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
            <a class="nav-link active" href="{{ route('laporan.index') }}"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Stok</a>
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
        
        <div class="filter-card">
            <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-bold"><i class="bi bi-calendar-event"></i> Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control" value="{{ $tglAwalFormatted }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-bold"><i class="bi bi-calendar-check"></i> Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tglAkhirFormatted }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter-circle"></i> Tampilkan Laporan</button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-light border w-100 text-center">Reset Filter</a>
                </div>
            </form>
        </div>

        <div class="btn-actions-main mb-3 d-flex justify-content-end gap-2">
            <button onclick="exportToExcel()" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Ekspor ke Excel (.xls)</button>
        </div>

        <div class="excel-sheet">
            
            <div class="kop-laporan">
                <h3>SPPG PUNGGUR BESAR</h3>
                <h4>LAPORAN STOCK BARANG (DETIL)</h4>
                <p>Periode : {{ \Carbon\Carbon::parse($tglAwalFormatted)->format('d-m-Y') }} s.d. {{ \Carbon\Carbon::parse($tglAkhirFormatted)->format('d-m-Y') }}</p>
                
            </div>

            <div class="table-responsive">
                <table class="excel-table" id="laporan-stok-table">
                    <thead>
                        <tr>
                            <th style="width: 30%">Nama Barang</th>
                            <th style="width: 10%">Satuan</th>
                            <th style="width: 10%">Saldo Awal</th>
                            <th style="width: 10%">Masuk</th>
                            <th style="width: 10%">Keluar</th>
                            <th style="width: 10%">Saldo Akhir</th>
                            <th style="width: 12%">Harga Beli Akhir</th>
                            <th style="width: 8%">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedBarangs = $barangs->groupBy(function($item) {
                                return $item->kategori->nama_kategori ?? 'UMUM';
                            });
                        @endphp

                        @forelse($groupedBarangs as $kategoriName => $items)
                            <!-- Baris Judul Kategori sesuai layout Excel -->
                            <tr class="category-header">
                                <td colspan="2" class="text-uppercase">{{ $kategoriName }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            
                            @foreach($items as $b)
                            <tr>
                                <td>{{ $b->nama_barang }}</td>
                                <td class="text-center">{{ $b->satuan }}</td>
                                <td class="text-center fw-bold text-secondary">{{ $b->saldo_awal > 0 ? number_format($b->saldo_awal) : '-' }}</td>
                                <td class="text-center text-success fw-bold">{{ $b->masuk > 0 ? number_format($b->masuk) : '-' }}</td>
                                <td class="text-center text-danger fw-bold">{{ $b->keluar > 0 ? number_format($b->keluar) : '-' }}</td>
                                <td class="text-center fw-bold text-primary">{{ $b->saldo_akhir > 0 ? number_format($b->saldo_akhir) : '-' }}</td>
                                <td class="text-center">{{ $b->harga_beli_akhir }}</td>
                                <td class="text-center">{{ $b->jumlah_rupiah }}</td>
                            </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                                    Tidak ada data persediaan barang untuk ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logika Ekspor HTML ke File Microsoft Excel (.xls) secara Instan
        function exportToExcel() {
            var table = document.getElementById("laporan-stok-table");
            var html = table.outerHTML;
            
            // Format styling inline untuk estetika file Excel
            var style = '<style>' +
                'table { border-collapse: collapse; }' +
                'th { background-color: #4f81bd; color: white; border: 1px solid #385d8a; padding: 5px; font-weight: bold; }' +
                'td { border: 1px solid #bfbfbf; padding: 5px; }' +
                '.category-header { background-color: #d9e1f2; font-weight: bold; }' +
                '</style>';
            
            // Membuat Kop Laporan persis di Excel
            var kopExcel = '<table>' +
                '<tr><td colspan="8" style="text-align:center; font-size:16px; font-weight:bold; font-family:Arial;">SPPG PUNGGUR BESAR</td></tr>' +
                '<tr><td colspan="8" style="text-align:center; font-size:14px; font-weight:bold; font-family:Arial;">LAPORAN STOCK BARANG (DETIL)</td></tr>' +
                '<tr><td colspan="8" style="text-align:center; font-size:11px; font-family:Arial;">Periode: {{ \Carbon\Carbon::parse($tglAwalFormatted)->format("d-m-Y") }} s.d {{ \Carbon\Carbon::parse($tglAkhirFormatted)->format("d-m-Y") }}</td></tr>' +
                '<tr><td colspan="8"></td></tr>' +
                '</table>';
            
            var fullHtml = kopExcel + style + html;
            var blob = new Blob(['\ufeff' + fullHtml], {
                type: "application/vnd.ms-excel;charset=utf-8"
            });
            
            var url = URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.href = url;
            a.download = "Laporan_Stok_SPPG_{{ $tglAwalFormatted }}_sd_{{ $tglAkhirFormatted }}.xls";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>
</body>
</html>
