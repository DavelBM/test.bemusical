<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewAdmin extends FormRequest
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
            'name'      => 'min:3|max:120|required',
            'email'     => 'required|string|email|max:255|unique:admins',
            'permission'=> 'digits:1|required',
            'password'  => 'required|string|min:6|confirmed',
        ];
    }
}
