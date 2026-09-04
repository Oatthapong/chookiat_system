<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(LoginRequest $request)
    {
        // ดึง username กับ password
        $credentials = $request->only('username', 'password');
        // ถ้ามี username กับ password
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Requirement 2.4: Check if user account is disabled
            if (!$user->is_active) {
                Auth::logout(); // ตรวจสอบ is_active พร้อมเตะออกทันทีถ้าเป็น false
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $errorMessage = 'บัญชีผู้ใช้นี้ถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                    ], 403);
                }

                return back()->withErrors([
                    'username' => $errorMessage,
                ])->withInput($request->only('username'));
            }

            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('dashboard'),
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        $invalidMessage = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $invalidMessage,
            ], 422);
        }

        return back()->withErrors([
            'username' => $invalidMessage,
        ])->withInput($request->only('username'));
    }

    /**
     * Show the forgot password request form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link to the given email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'กรุณากรอกอีเมลที่ใช้ลงทะเบียน',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.exists' => 'ไม่พบอีเมลนี้ในระบบ',
        ]);

        $token = \Illuminate\Support\Str::random(60);

        // Record or update token in password_reset_tokens table
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);

        // In a production environment, send email via Mail::to($request->email)->send(...)
        \Illuminate\Support\Facades\Log::info("Password Reset Link for {$request->email}: {$resetUrl}");

        return back()->with([
            'success' => 'สร้างลิงก์สำหรับรีเซ็ตรหัสผ่านเรียบร้อยแล้ว',
            'reset_url' => $resetUrl,
            'reset_email' => $request->email,
        ]);
    }

    /**
     * Show the password reset form.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        $email = $request->query('email');

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$record) {
            return redirect()->route('password.request')->with('error', 'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือถูกใช้งานไปแล้ว');
        }

        // Check if token has expired (valid for 60 minutes)
        if ($record->created_at && now()->diffInMinutes(\Carbon\Carbon::parse($record->created_at)) > 60) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')->with('error', 'ลิงก์รีเซ็ตรหัสผ่านหมดอายุแล้ว กรุณาขอใหม่อีกครั้ง');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.required' => 'กรุณาระบุอีเมล',
            'email.exists' => 'ไม่พบอีเมลนี้ในระบบ',
            'password.required' => 'กรุณากรอกรหัสผ่านใหม่',
            'password.min' => 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านยืนยันไม่ตรงกัน',
        ]);

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'โทเค็นสำหรับรีเซ็ตรหัสผ่านไม่ถูกต้อง']);
        }

        // Update user's password
        $user = \App\Models\User::where('email', $request->email)->first();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        // Delete used token
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'เปลี่ยนรหัสผ่านใหม่สำเร็จแล้ว กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('login'),
            ]);
        }

        return redirect()->route('login')->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
}
