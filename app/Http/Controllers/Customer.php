<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MCustomer as MCustomerModel;

class Customer extends Controller
{
    public function listAllCustomers()
    {
        try {
            $customers = MCustomerModel::all();

            $data = [
                'message' => 'Success fully fecthing data customers.',
                'data' => $customers
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while fetching customers.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }

    public function detailCustomer($idCustomer = NULL)
    {
        try {
            $customer = MCustomerModel::find($idCustomer);

            $data = [
                'message' => 'Success fully find data customer.',
                'data' => $customer
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while fetching customer.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }
}
