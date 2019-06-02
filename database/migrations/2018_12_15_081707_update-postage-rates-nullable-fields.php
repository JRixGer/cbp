<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePostageRatesNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postage_rates', function (Blueprint $table) {
            $table->string("description")->nullable()->change();
            $table->string("currency",3)->nullable()->change();
            $table->float("value",8,2)->nullable()->change();
            $table->string("service_code")->nullable()->change();
            $table->string("package_type")->nullable()->change();
            $table->float("other_cost",8,2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('postage_rates');
    }
}
