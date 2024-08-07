<?php

namespace App\Http\Controllers;

use App\Models\MCustomer as MCustomerModel;
use Illuminate\Http\Request;
use App\Models\TSales as TSalesModel;
use App\Models\TSalesDet as TSalesDetModel;
use App\Models\MBarang as MBarangModel;
use Carbon\Carbon;

class Transaksi extends Controller
{
    public function countTransaksi()
    {
        try {
            $dataTransaksi = TSalesModel::whereNotNull('cust_id')
                ->get();

            $data = [
                'message' => 'Successfully fetching no transaction data.',
                'data' => $dataTransaksi->count()
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while fetching no transaction.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }

    public function listTransaksi()
    {
        try {
            // Ambil data transaksi
            $transactions = TSalesModel::with('customer')
                ->whereNotNull('cust_id')
                ->get();

            // Format tanggal dalam data transaksi
            $formattedTransactions = $transactions->map(function ($transaction) {
                // Pastikan ada kolom 'tgl' untuk di-format
                if ($transaction->tgl) {
                    $date = Carbon::parse($transaction->tgl);
                    // Format tanggal menjadi `01-Agustus-2024`
                    $transaction->tgl = $date->translatedFormat('d-F-Y');
                }

                return $transaction;
            });

            $data = [
                'message' => 'Successfully fetching no transaction data.',
                'data' => $formattedTransactions
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while fetching no transaction.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }

    public function createNoTransaksi()
    {
        try {
            // Format tahun dan bulan saat ini
            $currentYearMonth = date('Ym'); // Format: 202101
            $prefix = $currentYearMonth . '-';

            // Format tanggal saat ini
            $formattedDate = now()->format('d-M-Y');

            // Cek apakah sudah ada nomor transaksi untuk hari ini
            $transaction = TSalesModel::where('kode', 'LIKE', $prefix . '%')
                ->whereNull('cust_id')
                ->first();

            if ($transaction) {
                // Jika transaksi sudah ada, update tanggal dan informasi lain jika diperlukan
                $transaction->tgl = $formattedDate;
                $transaction->save();
            } else {
                // Ambil nomor transaksi terakhir untuk bulan dan tahun ini
                $lastTransaction = TSalesModel::where('kode', 'LIKE', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();

                // Tentukan nomor transaksi berikutnya
                if ($lastTransaction) {
                    $lastNumber = (int)substr($lastTransaction->kode, -4);
                    $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $nextNumber = '0001';
                }

                // Buat kode transaksi baru
                $newCode = $prefix . $nextNumber;

                // Simpan kode transaksi baru ke database
                $transaction = TSalesModel::create([
                    'kode' => $newCode,
                    'tgl' => $formattedDate
                ]);
            }

            $data = [
                'message' => 'Successfully fetching no transaction data.',
                'data' => $transaction
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while fetching no transaction.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }

    public function createBarangTransaksi(Request $request)
    {
        try {
            $detailNoTranasksi = TSalesModel::where('kode', $request->input('kode_transaksi'))
                ->first();

            $detailBarang = MBarangModel::where('kode', $request->input('kode_barang'))
                ->first();

            $rumusDiskon = $detailBarang->harga * ($request->input('diskon_pct') / 100);
            $hasilDiskon = $detailBarang->harga - $rumusDiskon;

            $data = [
                'sales_id' => $detailNoTranasksi->id,
                'barang_id' => $detailBarang->id,
                'harga_bandrol' => $detailBarang->harga,
                'qty' => $request->input('qty'),
                'diskon_pct' => $request->input('diskon_pct'),
                'diskon_nilai' => $rumusDiskon,
                'harga_diskon' => $hasilDiskon,
                'total' => $hasilDiskon * $request->input('qty')
            ];

            $transaction = TSalesDetModel::create($data);

            $data = [
                'message' => 'Successfully add transaction data.',
                'data' => $transaction
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while add transaction.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }

    public function saveTransaksi(Request $request)
    {
        try {
            $detailNoTranasksi = TSalesModel::where('kode', $request->input('kode_transaksi'))
                ->first();

            $detailBarangTransaksi = TSalesDetModel::where('sales_id', $detailNoTranasksi->id)
                ->get();

            $subTotal = 0;

            foreach ($detailBarangTransaksi as $listBarang) {
                $subTotal += $listBarang->total;
            }

            $totalBayar = $subTotal - $request->input('diskon') - $request->input('ongkir');

            $detailCustomer = MCustomerModel::where('kode', $request->input('customer'))
                ->first();

            $dataSales = [
                'cust_id' => $detailCustomer->id,
                'subtotal' => $subTotal,
                'diskon' => $request->input('diskon'),
                'ongkir' => $request->input('ongkir'),
                'total_bayar' => $totalBayar
            ];

            TSalesModel::where('kode', $request->input('kode_transaksi'))
                ->update($dataSales);

            $data = [
                'message' => 'Successfully save transaction data.',
                'data' => $dataSales
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while save transaction.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }

    public function cancelTransaksi($kodeSales = NULL)
    {
        try {
            $detailTransaksi = TSalesModel::where('kode', $kodeSales)
                ->first();

            TSalesDetModel::where('sales_id', $detailTransaksi->id)
                ->delete();

            TSalesModel::where('kode', $kodeSales)
                ->delete();

            $data = [
                'message' => 'Successfully cancel transaction data.',
                'data' => [
                    'kode' => $kodeSales
                ]
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while cancel transaction.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }

    public function listBarangTransaksi($kodeTransaksi = NULL)
    {
        try {
            $detailNoTranasksi = TSalesModel::where('kode', $kodeTransaksi)
                ->first();

            $listBarangTransaksi = TSalesDetModel::where('sales_id', $detailNoTranasksi->id)
                ->with('m_barang')
                ->orderBy('created_at', 'ASC')
                ->get();

            $data = [
                'message' => 'Successfully fetching item transaction data.',
                'data' => $listBarangTransaksi
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while fetching item transaction.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }

    public function detailBarangTransaksi($idSalesDet = NULL)
    {
        try {
            $detailBarangTransaksi = TSalesDetModel::find($idSalesDet);

            $data = [
                'message' => 'Successfully fetching item transaction data.',
                'data' => $detailBarangTransaksi
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while fetching item transaction.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }

    public function deleteBarangTransaksi($idSalesDet = NULL)
    {
        try {
            $dataBarangTransaksi = TSalesDetModel::find($idSalesDet)->delete();

            $data = [
                'message' => 'Successfully delete item transaction data.',
                'data' => $dataBarangTransaksi
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while delete item transaction.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }
}
