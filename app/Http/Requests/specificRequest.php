<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class specificRequest extends FormRequest
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
            'name'         => 'min:3|max:120|required',
            'email'        => 'required|string|email|max:255',
            'company'      => 'min:5|max:120|required',
            'event_type'   => 'min:5|max:191|required',
            'day'          => 'date_format:"Y-m-d"|required',
            'time'         => 'date_format:"H:i"|required',
            'address'      => 'min:4|max:191|required',
            'duration'     => 'alpha_num|required',
        ];
    }
}
