@extends('layouts.app')

@section('title', 'Dashboard | Chookiat Leasing')

@section('content')
    <div class="container py-4">
        <!-- Welcome Banner -->
        <div class="card main-card mb-4 border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="fw-bold text-dark mb-1">
                            ยินดีต้อนรับ, {{ Auth::user()->name }}! 👋
                        </h3>
                        <p class="text-secondary mb-0">
                            เข้าสู่ระบบสำเร็จในฐานะ
                            @if (Auth::user()->isAdmin())
                                <span class="badge badge-role-admin">ผู้ดูแลระบบ (Admin)</span>
                            @else
                                <span class="badge badge-role-user">พนักงานทั่วไป (User)</span>
                            @endif
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <!-- User Profile Overview -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card main-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 fs-3 d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px;">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small fw-bold">ชื่อผู้ใช้งาน (Username)</h6>
                                <h5 class="fw-bold mb-0 text-dark">{{ Auth::user()->username }}</h5>
                            </div>
                        </div>
                        <hr class="text-muted opacity-25">

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card main-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 fs-3 d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px;">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small fw-bold">ระดับสิทธิ์ (Role)</h6>
                                <h5 class="fw-bold mb-0 text-dark">
                                    {{ Auth::user()->isAdmin() ? 'ผู้ดูแลระบบ (Admin)' : 'ผู้ใช้ทั่วไป (User)' }}
                                </h5>
                            </div>
                        </div>
                        <hr class="text-muted opacity-25">
                        <div class="small text-secondary">
                            <i class="bi bi-info-circle me-1"></i>
                            {{ Auth::user()->isAdmin() ? 'สามารถจัดการผู้ใช้และรถทั้งหมดได้' : 'สามารถจัดการคลังรถยนต์และคำนวณค่างวด' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card main-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 fs-3 d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small fw-bold">สถานะบัญชี (Status)</h6>
                                <h5 class="fw-bold mb-0 text-success">
                                    <i class="bi bi-dot fs-4"></i> พร้อมใช้งาน (Active)
                                </h5>
                            </div>
                        </div>
                        <hr class="text-muted opacity-25">
                        <div class="small text-secondary">
                            <i class="bi bi-shield-lock me-1"></i> ยืนยันตัวตนด้วย Session Authentication
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Navigation Modules -->
        <div class="row g-4">
            <!-- Car Inventory CRUD Module -->
            <div class="col-md-6">
                <div class="card main-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-car-front text-primary fs-4"></i>
                                <h5 class="fw-bold mb-0">ระบบจัดการคลังรถยนต์ (CRUD)</h5>
                            </div>
                            <span class="badge bg-secondary">หัวข้อถัดไป</span>
                        </div>
                        <p class="text-secondary small">
                            ระบบจัดการข้อมูลรถยนต์ (เพิ่ม ลบ แก้ไข ดูรายละเอียด) ด้วย jQuery AJAX แบบไม่ Refresh หน้าจอ
                        </p>
                        <button class="btn btn-outline-primary btn-sm disabled">
                            <i class="bi bi-arrow-right me-1"></i> เข้าสู่ระบบคลังรถ
                        </button>
                    </div>
                </div>
            </div>

            <!-- Installment Calculator Module -->
            <div class="col-md-6">
                <div class="card main-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-calculator text-success fs-4"></i>
                                <h5 class="fw-bold mb-0">ระบบคำนวณยอดผ่อนชำระ</h5>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">พร้อมใช้งาน</span>
                        </div>
                        <p class="text-secondary small">
                            คำนวณยอดผ่อน 12–84 งวด พร้อมระบบ Highlight งวดที่เลือก และ Alert ตรวจสอบยอดเงินแบบ Realtime
                        </p>
                        <a href="{{ route('installments.index') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-arrow-right me-1"></i> เข้าสู่หน้าคำนวณค่างวด
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
