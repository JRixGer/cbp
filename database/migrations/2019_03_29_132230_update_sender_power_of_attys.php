<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSenderPowerOfAttys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sender_power_of_attys', function (Blueprint $table) {
            DB::statement('ALTER TABLE sender_power_of_attys CHANGE sign_signature sign_signature LONGTEXT');
            DB::statement('ALTER TABLE sender_power_of_attys CHANGE signature_1 signature_1 LONGTEXT');
            DB::statement('ALTER TABLE sender_power_of_attys CHANGE signature_2 signature_2 LONGTEXT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sender_power_of_attys', function (Blueprint $table) {
            //
        });
    }
}
