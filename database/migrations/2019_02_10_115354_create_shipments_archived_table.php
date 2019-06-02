<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentsArchivedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments_archived', function (Blueprint $table) {
            $table->bigIncrements('archived_id');
            $table->string('id')->nullable();
            $table->string('shipment_code')->nullable();
            $table->string('sender_id')->nullable();
            $table->string('tracking_no')->nullable();
            $table->string('order_id')->nullable();
            $table->string('recipient_id')->nullable();
            $table->string('carrier')->nullable();
            $table->string('postage_rate_id')->nullable();
            $table->string('shipment_date')->nullable();
            $table->string('length')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('size_unit')->nullable();
            $table->string('weight')->nullable();
            $table->string('weight_unit')->nullable();
            $table->string('require_signature')->nullable();
            $table->string('delivery_fee')->nullable();
            $table->string('amount_paid')->nullable();
            $table->string('shipment_status_id')->nullable();
            $table->string('received')->nullable();
            $table->string('picked_up')->nullable();
            $table->string('in_transit')->nullable();
            $table->string('duration')->nullable();
            $table->string('delivered')->nullable();
            $table->string('note')->nullable();
            $table->string('letter_mail')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->string('deleted_at')->nullable();
            $table->string('import_batch')->nullable();
            $table->string('import_status')->nullable();
            $table->string('carrier_desc')->nullable();
            $table->string('currency')->nullable();
            $table->string('shipment_type')->nullable();
            $table->string('insurance_cover')->nullable();
            $table->string('insurance_cover_amount')->nullable();
            $table->string('cbp_address_id')->nullable();
            $table->string('letter_option')->nullable();
            $table->string('postage_fee')->nullable();
            $table->string('total_fee')->nullable();
            $table->string('postage')->nullable();
            $table->string('truck_fee')->nullable();
            $table->string('recipient_address_id')->nullable();
            $table->string('bag_id')->nullable();
            $table->string('pallet_id')->nullable();
            $table->string('cbme')->nullable();
            $table->string('carrier_id')->nullable();
            $table->string('mark_up')->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('coupon_type')->nullable();
            $table->string('coupon_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments_archive', function (Blueprint $table) {
            //
        });
    }
}
