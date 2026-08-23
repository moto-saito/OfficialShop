<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SalesSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認証チェックは auth.admin ミドルウェアで行うためここでは常に許可
        return true;
    }

    public function rules(): array
    {
        return [
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
            'status'    => 'nullable|in:pending,processing,shipped,completed,cancelled',
        ];
    }

    public function messages(): array
    {
        return [
            'date_to.after_or_equal' => '終了日は開始日以降の日付を指定してください。',
        ];
    }
}
