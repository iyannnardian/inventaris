<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';
    protected $primaryKey = 'id_barang'; // Menyesuaikan dengan migration kita
    protected $guarded = [];
    protected $appends = ['stok', 'kode_barang'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'id_barang', 'id_barang');
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class, 'id_barang', 'id_barang');
    }

    public function getStokAttribute()
    {
        $masuk = $this->barangMasuks()->sum('jumlah');
        $keluar = $this->barangKeluars()->sum('jumlah');
        return $this->stok_awal + $masuk - $keluar;
    }

    public function getKodeBarangAttribute()
    {
        $categoryName = optional($this->kategori)->nama_kategori ?? 'UMUM';
        // Ambil 2 huruf pertama dari nama kategori
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $categoryName), 0, 2));
        if (strlen($prefix) < 2) $prefix = 'BR';
        return $prefix . '.01.' . str_pad($this->id_barang, 3, '0', STR_PAD_LEFT);
    }
}
