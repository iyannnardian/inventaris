<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $totalMasuk = BarangMasuk::count();
        $totalKeluar = BarangKeluar::count();

        // Mengarahkan ke file view bernama dashboard.blade.php
        return view('dashboard', compact('totalBarang', 'totalMasuk', 'totalKeluar'));
    }
}
