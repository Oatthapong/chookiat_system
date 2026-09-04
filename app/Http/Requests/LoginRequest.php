<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Custom error messages in Thai.
     */
    // เป็นการตรวจฝั่ง Frontend ไม่ได้ไป Query Database
    public function messages(): array
    {
        return [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้ (Username)',
            'password.required' => 'กรุณากรอกรหัสผ่าน (Password)',
        ];
    }
}
