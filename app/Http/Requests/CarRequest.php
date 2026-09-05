<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs for validation (convert empty strings to null for nullable fields).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'car_code' => $this->car_code ? trim($this->car_code) : null,
            'brand' => $this->brand ? trim($this->brand) : null,
            'model' => $this->model ? trim($this->model) : null,
            'model_year' => !empty($this->model_year) ? (int) $this->model_year : null,
            'color' => !empty(trim($this->color ?? '')) ? trim($this->color) : null,
            'license_plate' => !empty(trim($this->license_plate ?? '')) ? trim($this->license_plate) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $carId = $this->route('car') ?? $this->route('id') ?? $this->input('id');

        return [
            'car_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('cars', 'car_code')->ignore($carId),
            ],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'model_year' => ['nullable', 'integer', 'digits:4', 'min:1990', 'max:' . (date('Y') + 1)],
            'color' => ['nullable', 'string', 'max:50'],
            'license_plate' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('cars', 'license_plate')->ignore($carId),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,reserved,sold,inactive'],
        ];
    }

    /**
     * Custom Thai error messages.
     */
    public function messages(): array
    {
        return [
            'car_code.required' => 'กรุณาระบุรหัสรถยนต์',
            'car_code.max' => 'รหัสรถยนต์ต้องไม่เกิน 30 ตัวอักษร',
            'car_code.unique' => 'รหัสรถยนต์นี้มีอยู่ในระบบแล้ว',
            'brand.required' => 'กรุณาระบุยี่ห้อรถยนต์',
            'brand.max' => 'ยี่ห้อรถยนต์ต้องไม่เกิน 100 ตัวอักษร',
            'model.required' => 'กรุณาระบุรุ่นรถยนต์',
            'model.max' => 'รุ่นรถยนต์ต้องไม่เกิน 100 ตัวอักษร',
            'model_year.digits' => 'ปีรถต้องเป็นปี ค.ศ. 4 หลัก (เช่น 2022)',
            'model_year.integer' => 'ปีรถต้องเป็นตัวเลขจำนวนเต็ม',
            'model_year.min' => 'ปีรถต้องไม่ต่ำกว่าปี 1990',
            'model_year.max' => 'ปีรถต้องไม่เกินปี ' . (date('Y') + 1),
            'color.max' => 'สีรถยนต์ต้องไม่เกิน 50 ตัวอักษร',
            'license_plate.max' => 'ทะเบียนรถต้องไม่เกิน 20 ตัวอักษร',
            'license_plate.unique' => 'ทะเบียนรถนี้มีอยู่ในระบบแล้ว',
            'price.required' => 'กรุณาระบุราคารถยนต์',
            'price.numeric' => 'ราคารถยนต์ต้องเป็นตัวเลขเท่านั้น',
            'price.min' => 'ราคารถยนต์ต้องไม่ติดลบ',
            'status.required' => 'กรุณาเลือกสถานะรถยนต์',
            'status.in' => 'สถานะรถยนต์ไม่ถูกต้อง',
        ];
    }
}
