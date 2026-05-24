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

            // 5. Dummy Harga Beli Akhir & Jumlah Rupiah sesuai layout Excel (bisa diisi dummy atau strip)
            $barang->harga_beli_akhir = '-';
            $barang->jumlah_rupiah = '-';

            return $barang;
        });

        // Format tanggal untuk dikirim ke view
        $tglAwalFormatted = $tanggalAwal->format('Y-m-d');
        $tglAkhirFormatted = $tanggalAkhir->format('Y-m-d');

        return view('laporan.index', compact('barangs', 'tglAwalFormatted', 'tglAkhirFormatted'));
    }
}
