<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_sales', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 15);
            $table->dateTime('tgl');
            $table->integer('cust_id');
            $table->decimal('subtotal', '32')->nullable();
            $table->decimal('diskon', '32')->nullable();
            $table->decimal('ongkir', '32')->nullable();
            $table->decimal('total_bayar', '32')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cust_id')->references('id')->on('m_customer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_sales');
    }
}
