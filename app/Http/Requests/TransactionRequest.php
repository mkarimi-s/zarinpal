<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $type = $this->segment(3);

        return !empty($type) && $this->checkRequestType($type);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|integer|not_in:0',
        ];
    }

    /**
     * @param string $type Type.
     *
     * @return bool
     */
    private function checkRequestType(string $type): bool
    {
        if ($type === 'pos') {
            $this->merge(['type' => Transaction::TYPE_POS]);
            return true;
        }
        if ($type === 'web') {
            $this->merge(['type' => Transaction::TYPE_WEB]);
            return true;
        }
        if ($type === 'mobile') {
            $this->merge(['type' => Transaction::TYPE_MOBILE]);
            return true;
        }

        return false;
    }
}
