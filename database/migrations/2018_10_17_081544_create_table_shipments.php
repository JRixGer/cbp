<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('shipment_code');
            $table->bigInteger('sender_id')->unsigned();
            $table->foreign('sender_id')->references('id')->on("senders");
            $table->string('tracking_no')->nullable();
            $table->string('order_id')->nullable();
            $table->bigInteger('recipient_id');
            $table->string('carrier_id')->nullable();
            $table->bigInteger('postage_rate_id')->nullable();
            $table->date('shipment_date')->nullable();
            $table->float('length',8,2);
            $table->float('width',8,2);
            $table->float('height',8,2);
            $table->string('size_unit',10);
            $table->float('weight',8,2);
            $table->string('weight_unit',10);
            $table->boolean('require_signature');
            $table->float('delivery_fee',8,2);
            $table->float('postage_fee',8,2);
            $table->float('amount_paid',8,2);
            $table->float('total_fee',8,2);
            $table->integer('shipment_status_id');
            $table->dateTimeTz('received')->nullable();
            $table->dateTimeTz('picked-up')->nullable();
            $table->dateTimeTz('in-transit')->nullable();
            $table->dateTimeTz('delivered')->nullable();
            $table->string('note')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments');
    }
}
