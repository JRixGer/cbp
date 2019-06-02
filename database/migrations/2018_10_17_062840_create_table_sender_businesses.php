<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSenderBusinesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sender_businesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sender_id')->unsigned();
            $table->foreign('sender_id')->references('id')->on("senders");
            $table->bigInteger("sender_mailing_address_id")->nullable();
            $table->string("business_name");
            $table->string("business_number");
            $table->string("business_location");
            $table->string("import_number")->nullable();
            $table->string("address_1");
            $table->string("address_2")->nullable();
            $table->string("city",20);
            $table->string("province",20);
            $table->string("postal",20);
            $table->string("country",20);
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
        Schema::dropIfExists('sender_businesses');
    }
}
