<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class SenderPowerOfAtty extends Model
{
    protected $table = 'sender_power_of_attys';
    protected $fillable = ["sender_id",
			             "business_name",
			             "business_number",
			             "address_1",
			             "address_2",
			             "city",
			             "country",
			             "province_state",
			             "full_name_of_corp_client",
			             "name_of_municipality",
			             "name_of_province_state",
			             "name_of_country",
			             "name_of_signing_authority_1",
			             "office_held_by_signing_authority_1",
			             "signature_1",
			             "name_of_signing_authority_2",
			             "office_held_by_signing_authority_2",
			             "signature_2",
			             "import_number",
			             "language",
			             "operating_trade",
			             "import_export_program_account_name",
			             "physical_business_location",
			             "physical_city",
			             "physical_province_state",
			             "physical_postal_zip_code",
			             "physical_country",
			             "contact_person_title",
			             "contact_person_first_name",
			             "contact_person_last_name",
			             "contact_person_work_tel_no",
			             "contact_person_ext",
			             "contact_person_work_fax_no",
			             "contact_person_mobile_no",
			             "transport",
			             "type_of_goods",
			             "estimated_annual_value",
			             "major_bus_description",
			             "major_bus_product_services_1",
			             "revenue_from_product_services_1",
			             "major_bus_product_services_2",
			             "revenue_from_product_services_2",
			             "major_bus_product_services_3",
			             "revenue_from_product_services_3",
			             "partner_type",
			             "sign_title",
			             "sign_first_name",
			             "sign_last_name",
			             "sign_tel_no",
			             "sign_signature"];

}
