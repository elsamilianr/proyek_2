<?php

namespace App\View\Components;

use Closure;
use App\Models\Keranjang;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class PembeliLayout extends Component
{
    public int $cartCount;

    public function __construct()
    {
        if (Auth::check()) {
            $keranjang = Keranjang::where('id_user', Auth::id())->first();
            $this->cartCount = $keranjang
                ? $keranjang->details()->sum('jumlah')
                : 0;
        } else {
            $this->cartCount = 0;
        }
    }

    public function render(): View|Closure|string
    {
        return view('layouts.pembeli', ['cartCount' => $this->cartCount]);
    }
}