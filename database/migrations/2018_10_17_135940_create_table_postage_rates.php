<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePostageRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postage_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("description");
            $table->string("currency",3);
            $table->float("value",8,2);
            $table->date("est_delivery_time")->nullable();
            $table->string("package_type");
            $table->string("service_code");
            $table->float("other_cost",8,2);
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
        Schema::dropIfExists('postage_rates');
    }
}
