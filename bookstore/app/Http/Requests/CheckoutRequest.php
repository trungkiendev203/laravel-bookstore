<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'note' => 'nullable|string|max:500',
            'payment_method' => 'required|in:cod,vnpay',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Họ và tên người nhận không được để trống.',
            'customer_name.string' => 'Họ và tên người nhận phải là chuỗi ký tự.',
            'customer_name.max' => 'Họ và tên người nhận không được vượt quá 255 ký tự.',
            'customer_phone.required' => 'Số điện thoại nhận hàng không được để trống.',
            'customer_phone.string' => 'Số điện thoại nhận hàng phải là chuỗi ký tự.',
            'customer_phone.max' => 'Số điện thoại nhận hàng không được vượt quá 20 ký tự.',
            'customer_address.required' => 'Địa chỉ nhận hàng không được để trống.',
            'customer_address.string' => 'Địa chỉ nhận hàng phải là chuỗi ký tự.',
            'customer_address.max' => 'Địa chỉ nhận hàng không được vượt quá 500 ký tự.',
            'note.string' => 'Ghi chú phải là chuỗi ký tự.',
            'note.max' => 'Ghi chú không được vượt quá 500 ký tự.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán đã chọn không hợp lệ.',
        ];
    }
}
