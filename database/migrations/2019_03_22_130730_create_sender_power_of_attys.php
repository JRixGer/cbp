<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSenderPowerOfAttys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sender_power_of_attys', function (Blueprint $table) {
            $table->bigIncrements('id');

             $table->string('sender_id')->nullable();
             $table->string('business_name')->nullable();
             $table->string('business_number')->nullable();
             $table->string('address_1')->nullable();
             $table->string('address_2')->nullable();
             $table->string('city')->nullable();
             $table->string('country')->nullable();
             $table->string('province_state')->nullable();
             $table->string('full_name_of_corp_client')->nullable();
             $table->string('name_of_municipality')->nullable();
             $table->string('name_of_province_state')->nullable();
             $table->string('name_of_country')->nullable();
             $table->string('name_of_signing_authority_1')->nullable();
             $table->string('office_held_by_signing_authority_1')->nullable();
             $table->string('signature_1')->nullable();
             $table->string('name_of_signing_authority_2')->nullable();
             $table->string('office_held_by_signing_authority_2')->nullable();
             $table->string('signature_2')->nullable();
             $table->string('import_number')->nullable();
             $table->string('language')->nullable();
             $table->string('operating_trade')->nullable();
             $table->string('import_export_program_account_name')->nullable();
             $table->string('physical_business_location')->nullable();
             $table->string('physical_city')->nullable();
             $table->string('physical_province_state')->nullable();
             $table->string('physical_postal_zip_code')->nullable();
             $table->string('physical_country')->nullable();
             $table->string('contact_person_title')->nullable();
             $table->string('contact_person_first_name')->nullable();
             $table->string('contact_person_last_name')->nullable();
             $table->string('contact_person_work_tel_no')->nullable();
             $table->string('contact_person_ext')->nullable();
             $table->string('contact_person_work_fax_no')->nullable();
             $table->string('contact_person_mobile_no')->nullable();
             $table->string('transport')->nullable();
             $table->string('type_of_goods')->nullable();
             $table->string('estimated_annual_value')->nullable();
             $table->string('major_bus_description')->nullable();
             $table->string('major_bus_product_services_1')->nullable();
             $table->string('revenue_from_product_services_1')->nullable();
             $table->string('major_bus_product_services_2')->nullable();
             $table->string('revenue_from_product_services_2')->nullable();
             $table->string('major_bus_product_services_3')->nullable();
             $table->string('revenue_from_product_services_3')->nullable();
             $table->string('partner_type')->nullable();
             $table->string('sign_title')->nullable();
             $table->string('sign_first_name')->nullable();
             $table->string('sign_last_name')->nullable();
             $table->string('sign_tel_no')->nullable();
             $table->string('sign_signature')->nullable();

            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));  
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
