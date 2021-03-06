<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateInfoUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name'   => 'min:3|max:120|required',
            'last_name'    => 'min:3|max:120|required',
            'about'        => 'min:10|max:2000|required',
            'bio'          => 'min:10|max:191|required',
            // 'phone'        => 'digits:10|required',
            'address'      => 'min:4|max:191|required',
            'degree'       => 'min:3|max:191|required',
            'location'     => 'min:2|max:191|required',
            'mile_radious' => 'digits_between:0,3|required',
            'place_id'     => 'required',
            // 'country'      => 'required',
        ];
    }
}
