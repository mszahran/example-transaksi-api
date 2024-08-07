<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MBarang as MBarangModel;

class Barang extends Controller
{
    public function listBarang()
    {
        try {
            $barang = MBarangModel::all();

            $data = [
                'message' => 'Success fully find data item.',
                'data' => $barang
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while fetching item.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }
}
