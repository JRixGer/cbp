<?php

namespace cbp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SenderMailingAddressRequest extends FormRequest
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
        return [
            'address_1' => "required",
            'city' => "required",
            'province' => "required",
            'country' => "required",
            'postal' => "required",

        ];
    }
}
