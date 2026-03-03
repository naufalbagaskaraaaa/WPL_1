<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $barangs = Barang::all();

            return DataTables::of($barangs)
                ->addColumn('harga_format', function ($barang) {
                    return 'Rp ' . number_format($barang->harga, 0, ',', '.');
                })
                ->addColumn('aksi', function ($barang) {
                    $edit = '<a href="' . route('barang.edit', $barang->id_barang) . '" 
                                class="btn btn-warning btn-sm">Edit</a>';

                    $hapus = '<form action="' . route('barang.destroy', $barang->id_barang) . '" 
                                    method="POST" style="display:inline"
                                    onsubmit="return confirm(\'Yakin hapus data ini?\')">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-danger btn-sm">Hapus</button>
                              </form>';

                    return $edit . ' ' . $hapus;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('barang.index');
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        DB::table('barang')->insert([
            'id_barang' => 'TEMP',
            'nama'      => $request->nama,
            'harga'     => $request->harga,
            'timestamp' => now(),
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit(string $id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update([
            'nama'  => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Data berhasil diupdate!');
    }

    public function destroy(string $id)
    {
        Barang::findOrFail($id)->delete();

        return redirect()->route('barang.index')
            ->with('success', 'Data berhasil dihapus!');
    }

    public function cetak(Request $request)
    {
        $request->validate([
            'selected'    => 'required|array|min:1',
            'selected.*'  => 'exists:barang,id_barang',
            'koordinat_x' => 'required|integer|min:1|max:5',
            'koordinat_y' => 'required|integer|min:1|max:8',
        ], [
            'selected.required' => 'Pilih minimal 1 data untuk dicetak!',
            'koordinat_x.max'   => 'Koordinat X maksimal 5 (kolom 1-5)',
            'koordinat_y.max'   => 'Koordinat Y maksimal 8 (baris 1-8)',
        ]);

        $barangs = Barang::whereIn('id_barang', $request->selected)->get()->toArray();

        $offset = ($request->koordinat_y - 1) * 5 + ($request->koordinat_x - 1);

        $semuaLabel = array_merge(
            array_fill(0, $offset, null),
            $barangs
        );

        $halaman = array_chunk($semuaLabel, 40);

        foreach ($halaman as $i => $h) {
            while (count($halaman[$i]) < 40) {
                $halaman[$i][] = null;
            }
        }

        $pdf = Pdf::loadView('barang.cetak', [
            'halaman' => $halaman,
            'x'       => $request->koordinat_x,
            'y'       => $request->koordinat_y,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('tag-harga.pdf');
    }
}
