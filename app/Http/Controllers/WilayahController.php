<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function getProvinces()
    {
        $provinces = DB::table('reg_provinces')->get();
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Berhasil mengambil data provinsi',
            'data' => $provinces
        ]);
    }

    public function getRegencies($id_provinsi)
    {
        $regencies = DB::table('reg_regencies')
            ->where('province_id', $id_provinsi)
            ->get();
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Berhasil mengambil data kota/kabupaten',
            'data' => $regencies
        ]);
    }

    public function getDistricts($id_kota)
    {
        $districts = DB::table('reg_districts')
            ->where('regency_id', $id_kota)
            ->get();
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Berhasil mengambil data kecamatan',
            'data' => $districts
        ]);
    }

    public function getVillages($id_kecamatan)
    {
        $villages = DB::table('reg_villages')
            ->where('district_id', $id_kecamatan)
            ->get();
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Berhasil mengambil data kelurahan/desa',
            'data' => $villages
        ]);
    }
}
