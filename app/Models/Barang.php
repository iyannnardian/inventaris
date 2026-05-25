<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';
    protected $primaryKey = 'id_barang'; // Menyesuaikan dengan migration kita
    protected $guarded = [];
    protected $appends = ['stok'];

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


}
