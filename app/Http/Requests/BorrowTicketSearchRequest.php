<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BorrowTicketSearchRequest extends FormRequest
{
    public const SORTABLE = ['id', 'ngay_muon', 'han_tra'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword'    => ['nullable', 'string', 'max:100'],   // tên độc giả
            'doc_gia_id' => ['nullable', 'integer', 'min:1'],
            'trang_thai' => ['nullable', Rule::in(['đang mượn', 'đã trả'])],
            'overdue'    => ['nullable', 'boolean'],              // true: đang mượn và đã quá hạn
            'from'       => ['nullable', 'date_format:Y-m-d'],
            'to'         => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'sort'       => ['nullable', Rule::in(self::SORTABLE)],
            'order'      => ['nullable', Rule::in(['asc', 'desc'])],
            'page'       => ['nullable', 'integer', 'min:1'],
            'per_page'   => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'trang_thai.in'        => 'Trạng thái chỉ nhận: đang mượn hoặc đã trả.',
            'from.date_format'     => 'Ngày bắt đầu phải có dạng YYYY-MM-DD.',
            'to.date_format'       => 'Ngày kết thúc phải có dạng YYYY-MM-DD.',
            'to.after_or_equal'    => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'sort.in'              => 'Chỉ được sắp xếp theo: ' . implode(', ', self::SORTABLE) . '.',
            'order.in'             => 'Thứ tự sắp xếp chỉ nhận asc hoặc desc.',
        ];
    }
}
