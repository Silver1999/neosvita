<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|min:3',
            'surname' => 'required|min:3',
            'patronymic' => 'required|min:3',
			'dob' => 'required|date_format:d-m-Y',
			
            'sex' => 'required|integer|min:1',
            'country' => 'required|integer|min:1',
            'city' => 'required|integer|min:1',
            'institution' => 'required|integer|min:1',
			'role' => 'required|integer|min:1',
			
            'group' => 'required|min:3',
            'email' => 'bail|required|min:3|email|unique:users',
            'pass' => 'required|min:6',
            'code' => 'required|min:3',
        ];
	}
	
	// public function messages()
    // {
    //     return [
    //         'name.min' => 'min length',
    //         'dob.date_format' => 'date format',
    //         // ..
    //     ];
    // }
}
