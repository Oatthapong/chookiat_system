<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminResetPasswordRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // ค้นหาตามชื่อ, username หรือ อีเมล
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ตัวกรองตามระดับสิทธิ์ (Role)
        if ($request->filled('role') && in_array($request->role, ['admin', 'user'])) {
            $query->where('role', $request->role);
        }

        // ตัวกรองตามสถานะ (Status)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->orderBy('id', 'asc')->get();

        // สรุปสถิติสำหรับ KPI Cards
        $kpi = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        // ถ้าเรียกผ่าน AJAX ให้ส่ง JSON กลับ
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'users' => $users,
                'kpi' => $kpi,
            ]);
        }

        return view('users.index', compact('users', 'kpi'));
    }

    /**
     * Display the specified user for editing.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => (bool)$user->is_active,
            ],
        ]);
    }

    /**
     * Requirement 2.4: Reset / Update ข้อมูลทั่วไปของผู้ใช้ (ชื่อ, username, email)
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'name' => trim($request->name),
            'username' => trim($request->username),
            'email' => trim($request->email),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'รีเซ็ต/อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว',
            'user' => $user,
        ]);
    }

    /**
     * Requirement 2.4: Reset รหัสผ่านของผู้ใช้โดย Admin
     */
    public function resetPassword(AdminResetPasswordRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "รีเซ็ตรหัสผ่านสำหรับผู้ใช้ '{$user->username}' เรียบร้อยแล้ว",
        ]);
    }

    /**
     * Requirement 2.4: ยกเลิกการใช้งาน / เปิดการใช้งาน User Login (สลับสถานะ is_active)
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // ป้องกันไม่ให้ Admin ระงับบัญชีของตัวเอง
        if ((int)$user->id === (int)Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถยกเลิกการใช้งานบัญชีที่กำลังเข้าสู่ระบบอยู่ได้',
            ], 422);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'เปิดการใช้งาน' : 'ยกเลิกการใช้งาน (ระงับบัญชี)';

        return response()->json([
            'success' => true,
            'message' => "{$statusText}ของ '{$user->username}' เรียบร้อยแล้ว",
            'is_active' => (bool)$user->is_active,
            'user' => $user,
        ]);
    }
}
