<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function indexJquery()
    {
        return view('pos.jquery');
    }

    public function indexAxios()
    {
        return view('pos.axios');
    }

    public function cariBarang($id_barang)
    {
        $barang = DB::table('barang')->where('id_barang', $id_barang)->first();
        
        if ($barang) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Barang ditemukan',
                'data' => $barang
            ]);
        }

        return response()->json([
            'status' => 'error',
            'code' => 404,
            'message' => 'Barang dengan kode tersebut tidak ditemukan',
            'data' => null
        ], 404);
    }

    public function simpanTransaksi(Request $request)
    {
        $items = $request->input('items', []);
        $total = $request->input('total', 0);

        if (count($items) === 0 || $total <= 0) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Keranjang kosong atau total tidak sah.',
                'data' => null
            ], 400);
        }

        DB::beginTransaction();

        try {
            $id_penjualan = DB::table('penjualan')->insertGetId([
                'timestamp' => now(), 
                'total' => $total
            ], 'id_penjualan');

            foreach ($items as $item) {
                DB::table('penjualan_detail')->insert([
                    'id_penjualan' => $id_penjualan,
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Transaksi berhasil disimpan!',
                'data' => [
                    'id_penjualan' => $id_penjualan
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Terjadi kesalahan internal: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}