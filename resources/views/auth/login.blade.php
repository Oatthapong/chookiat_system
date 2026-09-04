@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ | Chookiat Leasing')

@section('styles')
    <style>
        .login-container {
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            padding: 32px 24px;
            text-align: center;
            color: white;
        }

        .login-header .brand-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 12px;
            backdrop-filter: blur(4px);
        }

        .login-body {
            padding: 32px 28px;
        }

        .input-group-text {
            background-color: #f8fafc;
            border-right: none;
            color: #6c757d;
        }

        .form-control-custom {
            border-left: none;
            padding-left: 0;
        }

        .form-control-custom:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-radius: 0.375rem;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #86b7fe;
        }

        .btn-login {
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            background: #0d6efd;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .toggle-password {
            cursor: pointer;
            background: #f8fafc;
            border-left: none;
        }

        .toggle-password:hover {
            color: #0d6efd;
        }
    </style>
@endsection

@section('content')
    <div class="container login-container py-4">
        <div class="login-card">
            <!-- Brand Header -->
            <div class="login-header">
                <div class="brand-icon">
                    <i class="bi bi-car-front-fill"></i>
                </div>
                <h4 class="fw-bold mb-1">ชูเกียรติลิสซิ่ง</h4>
                <p class="mb-0 text-white-50 small">ระบบจัดการคลังรถยนต์และคำนวณค่างวด</p>
            </div>

            <!-- Form Body -->
            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2 mb-4"
                        role="alert">
                        <i class="bi bi-exclamation-circle-fill fs-5 text-danger shrink-0 mt-1"></i>
                        <div>
                            <div class="fw-semibold">ไม่สามารถเข้าสู่ระบบได้</div>
                            <ul class="mb-0 ps-3 small mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                <form id="loginForm" method="POST" action="{{ route('login.attempt') }}" novalidate>
                    @csrf

                    <!-- Username Field -->
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold text-secondary small">
                            ชื่อผู้ใช้ (Username) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="text"
                                class="form-control form-control-custom @error('username') is-invalid @enderror"
                                id="username" name="username" value="{{ old('username') }}"
                                placeholder="กรอกชื่อผู้ใช้ของคุณ" required autofocus autocomplete="username">
                        </div>
                        @error('username')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fw-semibold text-secondary small mb-0">
                                รหัสผ่าน (Password) <span class="text-danger">*</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="small text-decoration-none text-primary fw-medium" tabindex="-1">
                                ลืมรหัสผ่าน?
                            </a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password"
                                class="form-control form-control-custom @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="กรอกรหัสผ่าน" required
                                autocomplete="current-password">
                            <span class="input-group-text toggle-password" id="togglePasswordBtn" title="แสดง/ซ่อนรหัสผ่าน">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" id="submitBtn"
                            class="btn btn-primary btn-login d-flex align-items-center justify-content-center gap-2">
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <i class="bi bi-box-arrow-in-right" id="btnIcon"></i>
                            <span id="btnText">เข้าสู่ระบบ</span>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <small class="text-muted">
                        &copy; {{ date('Y') }} Chookiat Leasing Co., Ltd. All rights reserved.
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Show / Hide Password using jQuery
            // ซ่อน/แสดงรหัสผ่านเมื่อกดที่ไอคอนดวงตา
            $('#togglePasswordBtn').on('click', function() {
                const passwordInput = $('#password');
                const icon = $('#toggleIcon');

                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // Form Submit Loading State with jQuery
            $('#loginForm').on('submit', function() {
                $('#btnSpinner').removeClass('d-none');
                $('#btnIcon').addClass('d-none');
                $('#btnText').text('กำลังตรวจสอบข้อมูล...');
                $('#submitBtn').prop('disabled', true);
            });
        });
    </script>
@endsection
