<?php

namespace cbp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParcelDimensionsRequest extends FormRequest
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
            'length' => 'required',
            'width' => "required",
            'height' => "required",
            'weight' => "required"
        ];
    }
}
