<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Default periode: awal bulan ini s.d hari ini jika tanggal filter kosong
        $tanggalAwal = $request->filled('tanggal_awal') 
            ? Carbon::parse($request->tanggal_awal)->startOfDay() 
            : Carbon::now()->startOfMonth()->startOfDay();

        $tanggalAkhir = $request->filled('tanggal_akhir') 
            ? Carbon::parse($request->tanggal_akhir)->endOfDay() 
            : Carbon::now()->endOfDay();

        $barangs = Barang::with('kategori')->get()->map(function ($barang) use ($tanggalAwal, $tanggalAkhir) {
            // 1. Hitung Saldo Awal (Sebelum tanggal filter awal)
            // Saldo Awal = stok_awal + (Masuk sebelum tanggalAwal) - (Keluar sebelum tanggalAwal)
            $masukSebelum = $barang->barangMasuks()
                ->where('tanggal_masuk', '<', $tanggalAwal->format('Y-m-d'))
                ->sum('jumlah');

            $keluarSebelum = $barang->barangKeluars()
                ->where('tanggal_keluar', '<', $tanggalAwal->format('Y-m-d'))
                ->sum('jumlah');

            $barang->saldo_awal = $barang->stok_awal + $masukSebelum - $keluarSebelum;

            // 2. Hitung jumlah Masuk dalam periode
            $barang->masuk = $barang->barangMasuks()
                ->whereBetween('tanggal_masuk', [$tanggalAwal->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                ->sum('jumlah');

            // 3. Hitung jumlah Keluar dalam periode
            $barang->keluar = $barang->barangKeluars()
                ->whereBetween('tanggal_keluar', [$tanggalAwal->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                ->sum('jumlah');

            // 4. Hitung Saldo Akhir
            $barang->saldo_akhir = $barang->saldo_awal + $barang->masuk - $barang->keluar;

            // 5. Hitung Harga Beli Rata-rata Tertimbang (Weighted Average Cost) dari seluruh barang masuk
            $masuksUpToDate = $barang->barangMasuks()
                ->where('tanggal_masuk', '<=', $tanggalAkhir->format('Y-m-d'))
                ->get();

            $totalMasukQty = $masuksUpToDate->sum('jumlah');
            if ($totalMasukQty > 0) {
                $totalMasukValue = $masuksUpToDate->sum(function ($item) {
                    return $item->jumlah * $item->harga;
                });
                $hargaRataRata = $totalMasukValue / $totalMasukQty;
            } else {
                $hargaRataRata = 0; // Fallback ke 0 karena kolom harga dasar master barang ditiadakan
            }

            $barang->harga_beli_akhir = $hargaRataRata > 0 
                ? 'Rp ' . number_format($hargaRataRata, 0, ',', '.') 
                : '-';
            $barang->jumlah_rupiah = ($hargaRataRata > 0 && $barang->saldo_akhir > 0)
                ? 'Rp ' . number_format($barang->saldo_akhir * $hargaRataRata, 0, ',', '.') 
                : '-';

            return $barang;
        });

        // Format tanggal untuk dikirim ke view
        $tglAwalFormatted = $tanggalAwal->format('Y-m-d');
        $tglAkhirFormatted = $tanggalAkhir->format('Y-m-d');

        return view('laporan.index', compact('barangs', 'tglAwalFormatted', 'tglAkhirFormatted'));
    }
}
