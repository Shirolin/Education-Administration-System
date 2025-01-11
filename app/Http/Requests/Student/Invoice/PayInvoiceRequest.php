<?php

namespace App\Http\Requests\Student\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class PayInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'omise_token' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'omise_token.required' => '令牌缺失',
            'omise_token.string' => '令牌必须是字符串',
        ];
    }
}
