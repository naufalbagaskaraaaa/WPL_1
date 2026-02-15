<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function create() {
        return view('kategori.create');
    }

    public function store(Request $request) {
        //dd($request->all());
        Kategori::create([
            'nama_kategori' => $request->nama_kategori
        ]);
        return redirect()->back()->with('success','Kategori Berhasil ditambahkan sayang');
    }
}
