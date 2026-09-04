@extends('layouts.app')

@section('title', 'ลืมรหัสผ่าน | Chookiat Leasing')

@section('styles')
    <style>
        .forgot-container {
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .forgot-card {
            width: 100%;
            max-width: 440px;
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .forgot-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            padding: 28px 24px;
            text-align: center;
            color: white;
        }

        .forgot-header .brand-icon {
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

        .forgot-body {
            padding: 32px 28px;
        }

        .btn-forgot {
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            background: #0d6efd;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-forgot:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
    </style>
@endsection

@section('content')
    <div class="container forgot-container py-4">
        <div class="forgot-card">
            <!-- Brand Header -->
            <div class="forgot-header">
                <div class="brand-icon">
                    <i class="bi bi-key-fill"></i>
                </div>
                <h4 class="fw-bold mb-1">ลืมรหัสผ่าน</h4>
                <p class="mb-0 text-white-50 small">กรอกอีเมลที่ลงทะเบียนไว้เพื่อรับลิงก์รีเซ็ตรหัสผ่าน</p>
            </div>

            <!-- Body -->
            <div class="forgot-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show p-3 mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <span class="fw-bold">{{ session('success') }}</span>
                        </div>
                        <p class="small text-muted mb-2">ระบบได้สร้างโทเค็นยืนยันความปลอดภัยสำหรับ
                            <strong>{{ session('reset_email') }}</strong> เรียบร้อยแล้ว</p>

                        @if (session('reset_url'))
                            <div class="p-2 bg-white rounded border mt-2">
                                <span class="badge bg-primary mb-1">ลิงก์ทดสอบ (Demo Link)</span>
                                <div class="small text-break mb-2">
                                    <a href="{{ session('reset_url') }}" class="text-primary fw-bold text-decoration-none">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>
                                        คลิกที่นี่เพื่อไปหน้ารีเซ็ตรหัสผ่านทันที
                                    </a>
                                </div>
                                <small class="text-muted fst-italic">*สำหรับทดสอบในเครื่องโดยไม่ต้องตั้งค่า SMTP</small>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2 mb-4"
                        role="alert">
                        <i class="bi bi-exclamation-circle-fill fs-5 text-danger shrink-0 mt-1"></i>
                        <div>
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                <form id="forgotForm" method="POST" action="{{ route('password.email') }}" novalidate>
                    @csrf

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold text-secondary small">
                            อีเมลที่ลงทะเบียน (Email) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-envelope-fill text-secondary"></i>
                            </span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" placeholder="example@chookiat.com" required
                                autofocus autocomplete="email">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text small text-muted mt-1">
                            เช่น <code>admin@chookiat.com</code> หรือ <code>user1@chookiat.com</code>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" id="submitBtn"
                            class="btn btn-primary btn-forgot d-flex align-items-center justify-content-center gap-2">
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <i class="bi bi-send-fill" id="btnIcon"></i>
                            <span id="btnText">ส่งลิงก์รีเซ็ตรหัสผ่าน</span>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-3 pt-3 border-top">
                    <a href="{{ route('login') }}"
                        class="text-decoration-none text-secondary small fw-medium d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i> กลับไปหน้าเข้าสู่ระบบ
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#forgotForm').on('submit', function() {
                $('#btnSpinner').removeClass('d-none');
                $('#btnIcon').addClass('d-none');
                $('#btnText').text('กำลังประมวลผล...');
                $('#submitBtn').prop('disabled', true);
            });
        });
    </script>
@endsection
