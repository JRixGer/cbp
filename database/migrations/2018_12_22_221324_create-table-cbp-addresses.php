<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCbpAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cbp_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
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
        Schema::dropIfExists('cbp_addresses');
    }
}
