<?php

namespace App\Http\Requests;

use App\Services\BorrowService;
use Illuminate\Foundation\Http\FormRequest;

class BorrowTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Quyền đã được middleware role:1,2 kiểm tra ở tầng route
        return true;
    }

    public function rules(): array
    {
        return [
            'doc_gia_id'      => ['required', 'integer', 'min:1'],
            'sach'            => ['required', 'array', 'min:1',
                                  'max:' . BorrowService::MAX_TITLES_PER_TICKET],
            'sach.*.sach_id'  => ['required', 'integer', 'min:1', 'distinct'],
            'sach.*.so_luong' => ['required', 'integer', 'min:1',
                                  'max:' . BorrowService::MAX_QTY_PER_TITLE],
        ];
    }

    public function messages(): array
    {
        return [
            'doc_gia_id.required'      => 'Thiếu mã độc giả.',
            'doc_gia_id.integer'       => 'Mã độc giả phải là số nguyên.',
            'sach.required'            => 'Phải chọn ít nhất một cuốn sách.',
            'sach.array'               => 'Danh sách sách không hợp lệ.',
            'sach.min'                 => 'Phải chọn ít nhất một cuốn sách.',
            'sach.max'                 => 'Mỗi phiếu chỉ được mượn tối đa :max đầu sách.',
            'sach.*.sach_id.required'  => 'Thiếu mã sách.',
            'sach.*.sach_id.integer'   => 'Mã sách phải là số nguyên.',
            'sach.*.sach_id.distinct'  => 'Một đầu sách không được xuất hiện hai lần trong cùng phiếu.',
            'sach.*.so_luong.required' => 'Thiếu số lượng mượn.',
            'sach.*.so_luong.integer'  => 'Số lượng mượn phải là số nguyên.',
            'sach.*.so_luong.min'      => 'Số lượng mượn phải lớn hơn 0.',
            'sach.*.so_luong.max'      => 'Mỗi đầu sách chỉ được mượn tối đa :max cuốn.',
        ];
    }
}
