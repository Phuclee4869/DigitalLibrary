<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityLogSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword'      => ['nullable', 'string', 'max:100'],             // tìm trong mô tả
            'user_id'      => ['nullable', 'integer', 'min:1'],
            'hanh_dong'    => ['nullable', 'string', 'max:50', 'regex:/^[a-z_.]+$/'],
            'doi_tuong'    => ['nullable', 'string', 'max:50', 'regex:/^[a-z_]+$/'],
            'doi_tuong_id' => ['nullable', 'integer', 'min:1'],
            'from'         => ['nullable', 'date_format:Y-m-d'],
            'to'           => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'order'        => ['nullable', Rule::in(['asc', 'desc'])],
            'page'         => ['nullable', 'integer', 'min:1'],
            'per_page'     => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'hanh_dong.regex'   => 'Hành động chỉ gồm chữ thường, dấu chấm và gạch dưới (vd: borrow.create).',
            'doi_tuong.regex'   => 'Đối tượng chỉ gồm chữ thường và gạch dưới (vd: phieu_muon).',
            'from.date_format'  => 'Ngày bắt đầu phải có dạng YYYY-MM-DD.',
            'to.date_format'    => 'Ngày kết thúc phải có dạng YYYY-MM-DD.',
            'to.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ];
    }
}
