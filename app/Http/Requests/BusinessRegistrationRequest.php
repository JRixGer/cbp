<?php

namespace cbp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        if($this->get("with_import_number") == "yes"){
            return [
                'import_number' => 'required'
            ];
        }else{
            
            return [
                'business_name' => 'required',
                'business_number' => "required",
                'business_location' => "required",
                'address_1' => "required",
                'city' => "required",
                'province' => "required",
                'country' => "required",
                'postal' => "required",

            ];
        }
    }
}
