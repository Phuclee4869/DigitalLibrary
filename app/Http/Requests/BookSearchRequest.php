<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookSearchRequest extends FormRequest
{
    public const SORTABLE = ['id', 'ten_sach', 'tac_gia', 'so_luong_con_lai', 'created_at'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword'     => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'min:1'],
            'available'   => ['nullable', 'boolean'],
            'sort'        => ['nullable', Rule::in(self::SORTABLE)],
            'order'       => ['nullable', Rule::in(['asc', 'desc'])],
            'page'        => ['nullable', 'integer', 'min:1'],
            'per_page'    => ['nullable', 'integer', 'min:1'], // quá MAX_PER_PAGE sẽ bị cắt, không báo lỗi
        ];
    }

    public function messages(): array
    {
        return [
            'keyword.max'      => 'Từ khóa tối đa 100 ký tự.',
            'category_id.integer' => 'Mã thể loại phải là số nguyên.',
            'available.boolean'=> 'Tham số available chỉ nhận true/false.',
            'sort.in'          => 'Chỉ được sắp xếp theo: ' . implode(', ', self::SORTABLE) . '.',
            'order.in'         => 'Thứ tự sắp xếp chỉ nhận asc hoặc desc.',
            'page.integer'     => 'Số trang phải là số nguyên.',
            'page.min'         => 'Số trang bắt đầu từ 1.',
            'per_page.integer' => 'Số dòng mỗi trang phải là số nguyên.',
            'per_page.min'     => 'Số dòng mỗi trang phải lớn hơn 0.',
        ];
    }
}
