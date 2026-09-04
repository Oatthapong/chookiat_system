@extends('layouts.app')

@section('title', 'ตั้งรหัสผ่านใหม่ | Chookiat Leasing')

@section('styles')
<style>
    .reset-container {
        min-height: calc(100vh - 100px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reset-card {
        width: 100%;
        max-width: 440px;
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .reset-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        padding: 28px 24px;
        text-align: center;
        color: white;
    }

    .reset-header .brand-icon {
        width: 52px;
        height: 52px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 10px;
        backdrop-filter: blur(4px);
    }

    .reset-body {
        padding: 32px 28px;
    }

    .btn-reset {
        padding: 12px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        background: #0d6efd;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-reset:hover {
        background: #0b5ed7;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .toggle-password {
        cursor: pointer;
        background: #f8fafc;
    }
</style>
@endsection

@section('content')
<div class="container reset-container py-4">
    <div class="reset-card">
        <!-- Brand Header -->
        <div class="reset-header">
            <div class="brand-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h4 class="fw-bold mb-1">ตั้งรหัสผ่านใหม่</h4>
            <p class="mb-0 text-white-50 small">กำหนดรหัสผ่านใหม่สำหรับเข้าใช้งานระบบ</p>
        </div>

        <!-- Body -->
        <div class="reset-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill fs-5 text-danger shrink-0 mt-1"></i>
                    <div>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="resetForm" method="POST" action="{{ route('password.update') }}" novalidate>
                @csrf

                <!-- Hidden Token -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email Field (Readonly) -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-secondary small">
                        อีเมลบัญชีผู้ใช้ (Email)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-envelope text-secondary"></i>
                        </span>
                        <input type="email"
                               class="form-control bg-light @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', $email) }}"
                               readonly
                               required>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold text-secondary small">
                        รหัสผ่านใหม่ (New Password) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-lock text-secondary"></i>
                        </span>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="อย่างน้อย 6 ตัวอักษร"
                               required
                               autofocus
                               autocomplete="new-password">
                        <span class="input-group-text toggle-password" id="togglePasswordBtn" title="แสดง/ซ่อนรหัสผ่าน">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold text-secondary small">
                        ยืนยันรหัสผ่านใหม่ (Confirm Password) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-lock-fill text-secondary"></i>
                        </span>
                        <input type="password"
                               class="form-control"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="กรอกรหัสผ่านใหม่อีกครั้ง"
                               required
                               autocomplete="new-password">
                        <span class="input-group-text toggle-password" id="toggleConfirmPasswordBtn" title="แสดง/ซ่อนรหัสผ่าน">
                            <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                        </span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" id="submitBtn" class="btn btn-primary btn-reset d-flex align-items-center justify-content-center gap-2">
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <i class="bi bi-check2-circle" id="btnIcon"></i>
                        <span id="btnText">บันทึกรหัสผ่านใหม่</span>
                    </button>
                </div>
            </form>

            <div class="text-center mt-3 pt-3 border-top">
                <a href="{{ route('login') }}" class="text-decoration-none text-secondary small fw-medium">
                    <i class="bi bi-arrow-left me-1"></i> ยกเลิก และกลับไปหน้าเข้าสู่ระบบ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle New Password
        $('#togglePasswordBtn').on('click', function () {
            const input = $('#password');
            const icon = $('#toggleIcon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });

        // Toggle Confirm Password
        $('#toggleConfirmPasswordBtn').on('click', function () {
            const input = $('#password_confirmation');
            const icon = $('#toggleConfirmIcon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });

        // Submit Loading State
        $('#resetForm').on('submit', function () {
            $('#btnSpinner').removeClass('d-none');
            $('#btnIcon').addClass('d-none');
            $('#btnText').text('กำลังบันทึกรหัสผ่านใหม่...');
            $('#submitBtn').prop('disabled', true);
        });
    });
</script>
@endsection
