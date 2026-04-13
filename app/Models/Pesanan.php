<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $guarded = ['id'];

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class);
    }
}