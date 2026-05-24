<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang; // Memanggil model Barang
use App\Models\Kategori; // Memanggil model Kategori untuk filter nanti
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        // Query dengan eager loading kategori
        $query = Barang::with('kategori');

        // Pencarian barang
        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        // Filter Kategori
        if ($request->filled('id_kategori') && $request->id_kategori != 'all') {
            $query->where('id_kategori', $request->id_kategori);
        }

        $barangs = $query->get();
        $kategoris = Kategori::all();
        
        return view('barang.index', compact('barangs', 'kategoris'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'kepala dapur') {
            abort(403, 'Akses ditolak. Peran Kepala Dapur tidak memiliki wewenang untuk menambah barang.');
        }

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'id_kategori' => 'required|exists:kategoris,id_kategori',
            'stok_awal' => 'required|integer|min:0',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'id_kategori.required' => 'Kategori wajib dipilih.',
            'id_kategori.exists' => 'Kategori tidak valid.',
            'stok_awal.required' => 'Stok awal wajib diisi.',
            'stok_awal.integer' => 'Stok awal harus berupa angka.',
            'stok_awal.min' => 'Stok awal tidak boleh negatif.',
        ]);

        Barang::create([
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'id_kategori' => $request->id_kategori,
            'stok_awal' => $request->stok_awal,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role === 'kepala dapur') {
            abort(403, 'Akses ditolak. Peran Kepala Dapur tidak memiliki wewenang untuk mengubah barang.');
        }

        $barang = Barang::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'id_kategori' => 'required|exists:kategoris,id_kategori',
            'stok_awal' => 'required|integer|min:0',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'id_kategori.required' => 'Kategori wajib dipilih.',
            'id_kategori.exists' => 'Kategori tidak valid.',
            'stok_awal.required' => 'Stok awal wajib diisi.',
            'stok_awal.integer' => 'Stok awal harus berupa angka.',
            'stok_awal.min' => 'Stok awal tidak boleh negatif.',
        ]);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'id_kategori' => $request->id_kategori,
            'stok_awal' => $request->stok_awal,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diubah!');
    }

    public function destroy($id)
    {
        if (Auth::user()->role === 'kepala dapur') {
            abort(403, 'Akses ditolak. Peran Kepala Dapur tidak memiliki wewenang untuk menghapus barang.');
        }

        $barang = Barang::findOrFail($id);

        // Proteksi jika barang sudah memiliki riwayat transaksi masuk atau keluar
        if ($barang->barangMasuks()->count() > 0 || $barang->barangKeluars()->count() > 0) {
            return redirect()->route('barang.index')->with('error', 'Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }
}