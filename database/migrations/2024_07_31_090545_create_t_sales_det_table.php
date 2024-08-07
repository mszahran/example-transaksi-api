<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTSalesDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_sales_det', function (Blueprint $table) {
            $table->id();
            $table->integer('sales_id');
            $table->integer('barang_id');
            $table->decimal('harga_bandrol', '32');
            $table->decimal('qty', '32');
            $table->decimal('diskon_pct', '32');
            $table->decimal('diskon_nilai', '32');
            $table->decimal('harga_diskon', '32');
            $table->decimal('total');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sales_id')->references('id')->on('t_sales');
            $table->foreign('barang_id')->references('id')->on('m_barang');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_sales_det');
    }
}
