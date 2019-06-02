<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCsvImportTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_import_temp', function (Blueprint $table) {
            $table->increments('id');
            $table->text('import_batch')->nullable();
            $table->text('sender_id')->nullable();
            $table->text('firstName')->nullable();
            $table->text('lastName')->nullable();
            $table->text('company')->nullable();
            $table->text('businessName')->nullable();
            $table->text('addressLine1')->nullable();
            $table->text('addressLine2')->nullable();
            $table->text('city')->nullable();
            $table->text('provState')->nullable();
            $table->text('intlProvState')->nullable();
            $table->text('postalZipCode')->nullable();
            $table->text('country')->nullable();
            $table->text('email')->nullable();
            $table->text('phone')->nullable();
            $table->text('length')->nullable();
            $table->text('width')->nullable();
            $table->text('height')->nullable();
            $table->text('weight')->nullable();
            $table->text('weight_unit')->nullable();
            $table->text('tracking')->nullable();
            $table->text('isSignatureReq')->nullable();
            $table->text('letterMail')->nullable();
            $table->text('carrier')->nullable();
            $table->text('item1')->nullable();
            $table->text('qty1')->nullable();
            $table->text('itemValue1')->nullable();
            $table->text('originCountry1')->nullable();
            $table->text('item2')->nullable();
            $table->text('qty2')->nullable();
            $table->text('itemValue2')->nullable();
            $table->text('originCountry2')->nullable();
            $table->text('item3')->nullable();
            $table->text('qty3')->nullable();
            $table->text('itemValue3')->nullable();
            $table->text('originCountry3')->nullable();
            $table->text('item4')->nullable();
            $table->text('qty4')->nullable();
            $table->text('itemValue4')->nullable();
            $table->text('originCountry4')->nullable();
            $table->text('item5')->nullable();
            $table->text('qty5')->nullable();
            $table->text('itemValue5')->nullable();
            $table->text('originCountry5')->nullable();
            $table->text('item6')->nullable();
            $table->text('qty6')->nullable();
            $table->text('itemValue6')->nullable();
            $table->text('originCountry6')->nullable();
            $table->text('item7')->nullable();
            $table->text('qty7')->nullable();
            $table->text('itemValue7')->nullable();
            $table->text('originCountry7')->nullable();
            $table->text('item8')->nullable();
            $table->text('qty8')->nullable();
            $table->text('itemValue8')->nullable();
            $table->text('originCountry8')->nullable();
            $table->text('item9')->nullable();
            $table->text('qty9')->nullable();
            $table->text('itemValue9')->nullable();
            $table->text('originCountry9')->nullable();
            $table->text('item10')->nullable();
            $table->text('qty10')->nullable();
            $table->text('itemValue10')->nullable();
            $table->text('originCountry10')->nullable();
            $table->text('item11')->nullable();
            $table->text('qty11')->nullable();
            $table->text('itemValue11')->nullable();
            $table->text('originCountry11')->nullable();
            $table->text('item12')->nullable();
            $table->text('qty12')->nullable();
            $table->text('itemValue12')->nullable();
            $table->text('originCountry12')->nullable();
            $table->text('item13')->nullable();
            $table->text('qty13')->nullable();
            $table->text('itemValue13')->nullable();
            $table->text('originCountry13')->nullable();
            $table->text('item14')->nullable();
            $table->text('qty14')->nullable();
            $table->text('itemValue14')->nullable();
            $table->text('originCountry14')->nullable();
            $table->text('item15')->nullable();
            $table->text('qty15')->nullable();
            $table->text('itemValue15')->nullable();
            $table->text('originCountry15')->nullable();
            $table->text('item16')->nullable();
            $table->text('qty16')->nullable();
            $table->text('itemValue16')->nullable();
            $table->text('originCountry16')->nullable();
            $table->text('item17')->nullable();
            $table->text('qty17')->nullable();
            $table->text('itemValue17')->nullable();
            $table->text('originCountry17')->nullable();
            $table->text('item18')->nullable();
            $table->text('qty18')->nullable();
            $table->text('itemValue18')->nullable();
            $table->text('originCountry18')->nullable();
            $table->text('item19')->nullable();
            $table->text('qty19')->nullable();
            $table->text('itemValue19')->nullable();
            $table->text('originCountry19')->nullable();
            $table->text('item20')->nullable();
            $table->text('qty20')->nullable();
            $table->text('itemValue20')->nullable();
            $table->text('originCountry20')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csv_import_temp');
    }
}
