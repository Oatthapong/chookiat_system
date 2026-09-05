<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'cars';

    // ตาราง cars ไม่มี created_at และ updated_at
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * Note: ไม่รวม chassis_number ตามที่ปรับปรุงล่าสุด
     */
    protected $fillable = [
        'car_code',
        'brand',
        'model',
        'model_year',
        'color',
        'license_plate',
        'price',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'model_year' => 'integer',
    ];

    /**
     * Helper: สถานะภาษาไทย
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'พร้อมขาย',
            'reserved' => 'ติดจอง',
            'sold' => 'ขายแล้ว',
            'inactive' => 'ระงับการขาย',
            default => 'ไม่ระบุ',
        };
    }

    /**
     * Helper: Bootstrap Badge Class ตามสถานะ
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available' => 'bg-success',
            'reserved' => 'bg-warning text-dark',
            'sold' => 'bg-secondary',
            'inactive' => 'bg-danger',
            default => 'bg-light text-dark',
        };
    }
}