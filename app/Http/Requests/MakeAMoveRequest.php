<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakeAMoveRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'x' => 'required|int|min:0|max:2',
            'y' => 'required|int|min:0|max:2'
        ];
    }
}
