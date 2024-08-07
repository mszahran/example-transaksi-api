<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TSales extends Model
{
    use HasFactory;

    protected $table = "t_sales";
    protected $fillable = [
        "kode",
        "tgl",
        "cust_id",
        "subtotal",
        "diskon",
        "ongkir",
        "total_bayar"
    ];

    public function customer()
    {
        return $this->hasOne(MCustomer::class, 'id', 'cust_id');
    }
}
