<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class generalRequest extends FormRequest
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
            'name'         => 'min:2|max:120|required',
            'email'        => 'required|string|email|max:255',
            'company'      => 'min:2|max:120|required',
            'day'          => 'date_format:"Y-m-d"|required',
            'time'         => 'date_format:"H:i"|required',
            'duration'     => 'alpha_num|required',
            'address'      => 'min:4|max:191|required',
            'place_id'     => 'required',
            'type'         => 'required|in:soloist,ensemble',
            'comment'      => 'max:191',
        ];
    }
}
