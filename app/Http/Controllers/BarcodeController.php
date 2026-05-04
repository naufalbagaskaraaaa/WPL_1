<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('barcode.index');
    }

    public function cariBarang($id_barang)
    {
        $barang = Barang::find($id_barang);

        if ($barang) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Barang ditemukan',
                'data' => [
                    'id_barang' => $barang->id_barang,
                    'nama' => $barang->nama,
                    'harga' => $barang->harga
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'code' => 404,
            'message' => 'Barang tidak ditemukan',
            'data' => null
        ]);
    }
}
