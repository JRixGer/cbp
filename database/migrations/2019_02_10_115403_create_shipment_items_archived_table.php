<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentItemsArchivedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_items_archived', function (Blueprint $table) {
            $table->bigIncrements('archived_id');
            $table->string('id')->nullable();
            $table->string('shipment_id')->nullable();
            $table->string('pallet_id')->nullable();
            $table->string('bag_id')->nullable();
            $table->text('description')->nullable();
            $table->string('value')->nullable();
            $table->string('qty')->nullable();
            $table->string('origin_country')->nullable();
            $table->string('note')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->string('deleted_at')->nullable();         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_items_archive', function (Blueprint $table) {
            //
        });
    }
}
