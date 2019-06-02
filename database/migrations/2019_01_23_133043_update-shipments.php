<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {

            $table->float('delivery_fee',10,2)->nullable()->default(0)->change();
            $table->float('postage_fee',10,2)->nullable()->default(0)->change();
            $table->float('amount_paid',10,2)->nullable()->default(0)->change();
            $table->float('total_fee',10,2)->nullable()->default(0)->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            //
        });
    }
}
