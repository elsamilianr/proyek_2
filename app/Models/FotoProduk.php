<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoProduk extends Model
{
    protected $fillable = ['produk_id', 'foto'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}