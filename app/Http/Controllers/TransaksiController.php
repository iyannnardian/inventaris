<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $barangs = Barang::all();
        
        // Auto-seed supplier jika database kosong agar dropdown tidak kosong
        if (Supplier::count() === 0) {
            Supplier::create([
                'nama_supplier' => 'PT. Distributor Sembako Utama',
                'alamat' => 'Jl. Raya Industri No. 10, Jakarta'
            ]);
            Supplier::create([
                'nama_supplier' => 'CV. Pangan Makmur Abadi',
                'alamat' => 'Jl. Kemitraan No. 25, Bandung'
            ]);
        }
        
        $suppliers = Supplier::all();

        // Ambil data barang masuk
        $masuks = BarangMasuk::with(['barang', 'supplier', 'user'])->get()->map(function ($item) {
            $item->tipe = 'masuk';
            $item->tanggal = $item->tanggal_masuk;
            $item->id_transaksi = $item->id_masuk;
            return $item;
        });

        // Ambil data barang keluar
        $keluars = BarangKeluar::with(['barang', 'user'])->get()->map(function ($item) {
            $item->tipe = 'keluar';
            $item->tanggal = $item->tanggal_keluar;
            $item->id_transaksi = $item->id_keluar;
            $item->supplier = null;
            return $item;
        });

        // Gabungkan dan urutkan berdasarkan waktu pembuatan terbaru (descending)
        $transaksis = $masuks->concat($keluars)->sortByDesc('created_at');

        return view('transaksi.index', compact('barangs', 'suppliers', 'transaksis'));
    }

    public function storeMasuk(Request $request)
    {
        if (Auth::user()->role === 'kepala dapur') {
            abort(403, 'Akses ditolak. Peran Kepala Dapur tidak memiliki wewenang untuk mencatat transaksi masuk.');
        }

        $request->validate([
            'id_barang' => 'required|exists:barangs,id_barang',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
        ], [
            'id_barang.required' => 'Barang wajib dipilih.',
            'id_barang.exists' => 'Barang tidak valid.',
            'id_supplier.required' => 'Supplier wajib dipilih.',
            'id_supplier.exists' => 'Supplier tidak valid.',
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.integer' => 'Jumlah barang harus berupa angka.',
            'jumlah.min' => 'Jumlah barang minimal 1.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.date' => 'Format tanggal tidak valid.',
        ]);

        BarangMasuk::create([
            'id_barang' => $request->id_barang,
            'id_supplier' => $request->id_supplier,
            'jumlah' => $request->jumlah,
            'tanggal_masuk' => $request->tanggal_masuk,
            'id_user' => Auth::id(),
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi barang masuk berhasil dicatat!');
    }

    public function storeKeluar(Request $request)
    {
        if (Auth::user()->role === 'kepala dapur') {
            abort(403, 'Akses ditolak. Peran Kepala Dapur tidak memiliki wewenang untuk mencatat transaksi keluar.');
        }

        $request->validate([
            'id_barang' => 'required|exists:barangs,id_barang',
            'jumlah' => 'required|integer|min:1',
            'tanggal_keluar' => 'required|date',
        ], [
            'id_barang.required' => 'Barang wajib dipilih.',
            'id_barang.exists' => 'Barang tidak valid.',
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.integer' => 'Jumlah barang harus berupa angka.',
            'jumlah.min' => 'Jumlah barang minimal 1.',
            'tanggal_keluar.required' => 'Tanggal keluar wajib diisi.',
            'tanggal_keluar.date' => 'Format tanggal tidak valid.',
        ]);

        $barang = Barang::findOrFail($request->id_barang);

        // Validasi apakah stok mencukupi
        if ($barang->stok < $request->jumlah) {
            return redirect()->route('transaksi.index')
                ->with('error', "Stok tidak mencukupi! Stok saat ini untuk {$barang->nama_barang} adalah {$barang->stok} {$barang->satuan}.")
                ->withInput();
        }

        BarangKeluar::create([
            'id_barang' => $request->id_barang,
            'jumlah' => $request->jumlah,
            'tanggal_keluar' => $request->tanggal_keluar,
            'id_user' => Auth::id(),
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi barang keluar berhasil dicatat!');
    }

    public function destroyMasuk($id)
    {
        if (Auth::user()->role === 'kepala dapur') {
            abort(403, 'Akses ditolak. Peran Kepala Dapur tidak memiliki wewenang untuk menghapus transaksi masuk.');
        }

        $masuk = BarangMasuk::findOrFail($id);
        
        // Proteksi agar penghapusan barang masuk tidak menyebabkan stok menjadi negatif
        $barang = $masuk->barang;
        if (($barang->stok - $masuk->jumlah) < 0) {
            return redirect()->route('transaksi.index')->with('error', 'Transaksi tidak dapat dihapus karena akan menyebabkan stok barang menjadi negatif.');
        }

        $masuk->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi barang masuk berhasil dihapus!');
    }

    public function destroyKeluar($id)
    {
        if (Auth::user()->role === 'kepala dapur') {
            abort(403, 'Akses ditolak. Peran Kepala Dapur tidak memiliki wewenang untuk menghapus transaksi keluar.');
        }

        $keluar = BarangKeluar::findOrFail($id);
        $keluar->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi barang keluar berhasil dihapus!');
    }
}