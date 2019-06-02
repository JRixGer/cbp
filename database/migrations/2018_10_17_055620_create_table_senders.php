<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('senders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name',30)->nullable();
            $table->string('last_name',30)->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->boolean('has_business')->default(0);
            $table->string('referral',60)->nullable();
            $table->boolean('marketing_emails')->default(0);
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
        Schema::dropIfExists('senders');
    }
}
