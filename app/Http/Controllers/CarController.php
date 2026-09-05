<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarRequest;
use App\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarController extends Controller
{
    /**
     * Display a listing of cars or return JSON data for AJAX.
     */
    public function index(Request $request): View|JsonResponse
    {
        // ถ้าเป็น AJAX หรือขอ JSON ให้ส่งข้อมูลรถยนต์และสถิติ KPI กลับไป
        if ($request->ajax() || $request->wantsJson() || $request->has('json')) {
            $query = Car::query();

            // ค้นหาคำสำคัญ (รหัสรถ, ยี่ห้อ, รุ่น, ทะเบียน)
            if ($request->filled('keyword')) {
                $keyword = trim($request->keyword);
                $query->where(function ($q) use ($keyword) {
                    $q->where('car_code', 'like', "%{$keyword}%")
                      ->orWhere('brand', 'like', "%{$keyword}%")
                      ->orWhere('model', 'like', "%{$keyword}%")
                      ->orWhere('license_plate', 'like', "%{$keyword}%");
                });
            }

            // กรองตามสถานะ
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // กรองตามยี่ห้อ
            if ($request->filled('brand') && $request->brand !== 'all') {
                $query->where('brand', $request->brand);
            }

            $cars = $query->orderBy('id', 'desc')->get()->map(function ($car) {
                return [
                    'id' => $car->id,
                    'car_code' => $car->car_code,
                    'brand' => $car->brand,
                    'model' => $car->model,
                    'model_year' => $car->model_year,
                    'color' => $car->color,
                    'license_plate' => $car->license_plate,
                    'price' => (float) $car->price,
                    'formatted_price' => number_format($car->price, 2),
                    'status' => $car->status,
                    'status_label' => $car->status_label,
                    'status_badge' => $car->status_badge,
                ];
            });

            // คำนวณสรุปสถิติ KPI ในคลังรถ
            $summary = [
                'total' => Car::count(),
                'available' => Car::where('status', 'available')->count(),
                'reserved' => Car::where('status', 'reserved')->count(),
                'sold' => Car::where('status', 'sold')->count(),
                'inactive' => Car::where('status', 'inactive')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $cars,
                'summary' => $summary,
            ]);
        }

        // ดึงรายการยี่ห้อรถทั้งหมดที่มีในระบบสำหรับ Dropdown filter
        $brands = Car::select('brand')->distinct()->orderBy('brand')->pluck('brand');

        return view('cars.index', compact('brands'));
    }

    /**
     * Store a newly created car in storage via AJAX.
     */
    public function store(CarRequest $request): JsonResponse
    {
        $car = Car::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'เพิ่มข้อมูลรถยนต์รหัส ' . $car->car_code . ' สำเร็จแล้ว',
            'data' => $car,
        ], 201);
    }

    /**
     * Display the specified car details for AJAX modal.
     */
    public function show(int $id): JsonResponse
    {
        $car = Car::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $car->id,
                'car_code' => $car->car_code,
                'brand' => $car->brand,
                'model' => $car->model,
                'model_year' => $car->model_year,
                'color' => $car->color,
                'license_plate' => $car->license_plate,
                'price' => (float) $car->price,
                'formatted_price' => number_format($car->price, 2),
                'status' => $car->status,
                'status_label' => $car->status_label,
                'status_badge' => $car->status_badge,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified car (returns JSON for AJAX).
     */
    public function edit(int $id): JsonResponse
    {
        $car = Car::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $car,
        ]);
    }

    /**
     * Update the specified car in storage via AJAX.
     */
    public function update(CarRequest $request, int $id): JsonResponse
    {
        $car = Car::findOrFail($id);
        $car->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'แก้ไขข้อมูลรถยนต์รหัส ' . $car->car_code . ' เรียบร้อยแล้ว',
            'data' => $car,
        ]);
    }

    /**
     * Remove the specified car from storage via AJAX.
     */
    public function destroy(int $id): JsonResponse
    {
        $car = Car::findOrFail($id);
        $code = $car->car_code;
        $car->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลรถยนต์รหัส ' . $code . ' สำเร็จแล้ว',
        ]);
    }
}
