<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransacaoRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'value' => 'required|numeric',
            'payer' => 'required|exists:usuarios,id',
            'payee' => 'required|exists:usuarios,id',
        ];
    }
}
