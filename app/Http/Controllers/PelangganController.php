<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pelanggan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    // Halaman Data Customer
    public function index()
    {
        $pelanggans = Pelanggan::all();
        return view('pelanggan.index', compact('pelanggans'));
    }

    // Tambah Customer 1 (Simpan BLOB)
    public function createBlob()
    {
        return view('pelanggan.create_blob');
    }

    public function storeBlob(Request $request)
    {
        $request->validate([
            'nama'      => 'required',
            'alamat'    => 'required',
            'provinsi'  => 'required',
            'kota'      => 'required',
            'kecamatan' => 'required',
            'kodepos'   => 'required',
            'foto'      => 'required' // berupa string base64 / data blob
        ]);

        Pelanggan::create([
            'nama'      => $request->nama,
            'alamat'    => $request->alamat,
            'provinsi'  => $request->provinsi,
            'kota'      => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kodepos'   => $request->kodepos,
            'foto'      => $request->foto, // insert directly
            'tipe_simpan'=> 'blob',
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil ditambahkan (via BLOB) !');
    }

    // Tambah Customer 2 (Simpan File Fisik)
    public function createFile()
    {
        return view('pelanggan.create_file');
    }

    public function storeFile(Request $request)
    {
        $request->validate([
            'nama'      => 'required',
            'alamat'    => 'required',
            'provinsi'  => 'required',
            'kota'      => 'required',
            'kecamatan' => 'required',
            'kodepos'   => 'required',
            'foto'      => 'required' // base64 string dari frontend
        ]);

        // Proses decode base64 -> Image File
        $imageParts = explode(';base64,', $request->foto);
        $imageTypeAux = explode('image/', $imageParts[0]);
        $imageType = $imageTypeAux[1];
        $imageBase64 = base64_decode($imageParts[1]);
        $fileName = 'pelanggan_' . time() . '.' . $imageType;
        
        // Simpan file fisik ke disk 'public' (storage/app/public/pelanggan/)
        Storage::disk('public')->put('pelanggan/' . $fileName, $imageBase64);

        $filePath = 'storage/pelanggan/' . $fileName; // path untuk diakses via web

        Pelanggan::create([
            'nama'      => $request->nama,
            'alamat'    => $request->alamat,
            'provinsi'  => $request->provinsi,
            'kota'      => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kodepos'   => $request->kodepos,
            'foto'      => $filePath, // insert path string
            'tipe_simpan'=> 'fisik',
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil ditambahkan (Fisik) !');
    }
}
