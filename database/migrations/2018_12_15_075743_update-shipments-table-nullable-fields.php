<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsTableNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('shipment_code')->nullable()->change();
            $table->float('length',8,2)->nullable()->change();
            $table->float('width',8,2)->nullable()->change();
            $table->float('height',8,2)->nullable()->change();
            $table->string('size_unit',10)->nullable()->change();
            $table->float('weight',8,2)->nullable()->change();
            $table->string('weight_unit',10)->nullable()->change();
            $table->boolean('require_signature')->nullable()->change();
            $table->float('delivery_fee',8,2)->nullable()->change();
            $table->float('postage_fee',8,2)->nullable()->change();
            $table->float('amount_paid',8,2)->nullable()->change();
            $table->float('total_fee',8,2)->nullable()->change();
            $table->integer('shipment_status_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // public function down()
    // {
    //     Schema::dropIfExists('shipments');
    // }
}
