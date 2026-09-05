@extends('layouts.app')

@section('title', 'จัดการคลังรถยนต์ (CRUD) | Chookiat Leasing')

@section('styles')
    <style>
        .kpi-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #ffffff;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .table-cars th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.88rem;
            vertical-align: middle;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-cars td {
            vertical-align: middle;
            font-size: 0.93rem;
        }

        .table-cars tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        .action-btn-group .btn {
            padding: 0.28rem 0.55rem;
            font-size: 0.85rem;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="container py-4">

        <!-- Header & Action Bar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">
                    <i class="bi bi-car-front-fill text-primary me-2"></i>ระบบจัดการคลังรถยนต์ (Car Inventory)
                </h3>
                <p class="text-secondary small mb-0">
                    จัดการข้อมูลรถยนต์ (เพิ่ม ลบ แก้ไข ค้นหา) ด้วย <strong>jQuery AJAX</strong> โดยไม่ Refresh หน้าเว็บ
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-primary fw-semibold shadow-sm d-flex align-items-center gap-2"
                    id="btnOpenAddModal">
                    <i class="bi bi-plus-circle-fill fs-5"></i>
                    <span>เพิ่มรถใหม่</span>
                </button>
                <a href="{{ route('installments.index') }}"
                    class="btn btn-outline-success fw-semibold shadow-sm d-flex align-items-center gap-1">
                    <i class="bi bi-calculator"></i>
                    <span>คำนวณค่างวด</span>
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> กลับสู่ Dashboard
                </a>
            </div>
        </div>

        <!-- Alert Message Toast Container -->
        <div id="ajaxAlertContainer" class="mb-3"></div>

        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <!-- รถทั้งหมด -->
            <div class="col-6 col-md-3">
                <div class="card kpi-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">รถทั้งหมด</span>
                            <h4 class="fw-bold mb-0 text-dark" id="kpiTotal">0</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- พร้อมขาย -->
            <div class="col-6 col-md-3">
                <div class="card kpi-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">พร้อมขาย (Available)</span>
                            <h4 class="fw-bold mb-0 text-success" id="kpiAvailable">0</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ติดจอง -->
            <div class="col-6 col-md-3">
                <div class="card kpi-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">ติดจอง (Reserved)</span>
                            <h4 class="fw-bold mb-0 text-warning" id="kpiReserved">0</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ขายแล้ว -->
            <div class="col-6 col-md-3">
                <div class="card kpi-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-secondary bg-opacity-10 text-secondary">
                            <i class="bi bi-cart-check-fill"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">ขายแล้ว (Sold)</span>
                            <h4 class="fw-bold mb-0 text-secondary" id="kpiSold">0</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Toolbar -->
        <div class="card main-card mb-4 p-3 border-0">
            <div class="row g-2 align-items-center">
                <!-- ค้นหาคำสำคัญ -->
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-secondary">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="filterKeyword" class="form-control border-start-0 bg-light"
                            placeholder="ค้นหารหัสรถ, ยี่ห้อ, รุ่น หรือทะเบียน..." autocomplete="off">
                    </div>
                </div>

                <!-- กรองสถานะ -->
                <div class="col-md-3">
                    <select id="filterStatus" class="form-select bg-light">
                        <option value="all">สถานะทั้งหมด</option>
                        <option value="available">🟢 พร้อมขาย (Available)</option>
                        <option value="reserved">🟡 ติดจอง (Reserved)</option>
                        <option value="sold">⚪ ขายแล้ว (Sold)</option>
                        <option value="inactive">🔴 ระงับการขาย (Inactive)</option>
                    </select>
                </div>

                <!-- กรองยี่ห้อ -->
                <div class="col-md-3">
                    <select id="filterBrand" class="form-select bg-light">
                        <option value="all">ยี่ห้อทั้งหมด</option>
                        @foreach ($brands as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- ปุ่มล้างตัวกรอง -->
                <div class="col-md-1 text-end">
                    <button type="button" id="btnResetFilters" class="btn btn-outline-secondary w-100"
                        title="รีเซ็ตตัวกรอง">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Car List Table Card -->
        <div class="card main-card position-relative overflow-hidden">
            <!-- Loading Spinner Overlay -->
            <div id="tableLoading" class="loading-overlay d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลดข้อมูล...</span>
                </div>
            </div>

            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-table text-primary me-2"></i>รายการรถยนต์ในคลัง
                </h5>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"
                    id="recordsCountBadge">
                    กำลังโหลด...
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-cars mb-0" id="carsTable">
                        <thead>
                            <tr>
                                <th style="width: 12%;">รหัสรถ</th>
                                <th style="width: 25%;">ยี่ห้อ / รุ่น</th>
                                <th style="width: 8%;" class="text-center">ปี</th>
                                <th style="width: 10%;">สี</th>
                                <th style="width: 13%;">ทะเบียน</th>
                                <th style="width: 14%;" class="text-end">ราคา (บาท)</th>
                                <th style="width: 10%;" class="text-center">สถานะ</th>
                                <th style="width: 8%;" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="carsTableBody">
                            <!-- AJAX populates rows here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: เพิ่ม / แก้ไขข้อมูลรถยนต์ (Add/Edit Car Modal) -->
    <!-- ======================================================== -->
    <div class="modal fade" id="carModal" tabindex="-1" aria-labelledby="carModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="carModalTitle">
                        <i class="bi bi-plus-circle me-2"></i>เพิ่มข้อมูลรถยนต์ใหม่
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="carForm" novalidate>
                    <div class="modal-body p-4">
                        <input type="hidden" id="carId" name="id">

                        <div class="row g-3">
                            <!-- รหัสรถ -->
                            <div class="col-md-6">
                                <label for="car_code" class="form-label fw-semibold small text-secondary">
                                    รหัสรถยนต์ (Car Code) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                    <input type="text" class="form-control" id="car_code" name="car_code"
                                        placeholder="เช่น CAR-001" required>
                                    <div class="invalid-feedback" id="error_car_code"></div>
                                </div>
                            </div>

                            <!-- ทะเบียนรถ -->
                            <div class="col-md-6">
                                <label for="license_plate" class="form-label fw-semibold small text-secondary">
                                    ทะเบียนรถ (License Plate)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control" id="license_plate" name="license_plate"
                                        placeholder="เช่น กข-1234 สงขลา">
                                    <div class="invalid-feedback" id="error_license_plate"></div>
                                </div>
                            </div>

                            <!-- ยี่ห้อรถ -->
                            <div class="col-md-6">
                                <label for="brand" class="form-label fw-semibold small text-secondary">
                                    ยี่ห้อ (Brand) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" class="form-control" id="brand" name="brand"
                                        placeholder="เช่น Toyota, Honda, Isuzu" required>
                                    <div class="invalid-feedback" id="error_brand"></div>
                                </div>
                            </div>

                            <!-- รุ่นรถ -->
                            <div class="col-md-6">
                                <label for="model" class="form-label fw-semibold small text-secondary">
                                    รุ่น (Model) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-car-front"></i></span>
                                    <input type="text" class="form-control" id="model" name="model"
                                        placeholder="เช่น Hilux Revo, City, D-Max" required>
                                    <div class="invalid-feedback" id="error_model"></div>
                                </div>
                            </div>

                            <!-- ปีรถ -->
                            <div class="col-md-4">
                                <label for="model_year" class="form-label fw-semibold small text-secondary">
                                    ปี ค.ศ. (Model Year)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="number" class="form-control" id="model_year" name="model_year"
                                        placeholder="เช่น 2022" min="1990" max="{{ date('Y') + 1 }}">
                                    <div class="invalid-feedback" id="error_model_year"></div>
                                </div>
                            </div>

                            <!-- สีรถ -->
                            <div class="col-md-4">
                                <label for="color" class="form-label fw-semibold small text-secondary">
                                    สีรถ (Color)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-palette"></i></span>
                                    <input type="text" class="form-control" id="color" name="color"
                                        placeholder="เช่น ขาวมุก, ดำ, บรอนซ์เงิน">
                                    <div class="invalid-feedback" id="error_color"></div>
                                </div>
                            </div>

                            <!-- สถานะ -->
                            <div class="col-md-4">
                                <label for="status" class="form-label fw-semibold small text-secondary">
                                    สถานะ (Status) <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="available" selected>🟢 พร้อมขาย (Available)</option>
                                    <option value="reserved">🟡 ติดจอง (Reserved)</option>
                                    <option value="sold">⚪ ขายแล้ว (Sold)</option>
                                    <option value="inactive">🔴 ระงับการขาย (Inactive)</option>
                                </select>
                                <div class="invalid-feedback" id="error_status"></div>
                            </div>

                            <!-- ราคารถ -->
                            <div class="col-md-12">
                                <label for="price" class="form-label fw-semibold small text-secondary">
                                    ราคารถยนต์ (Price) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="price" name="price" placeholder="เช่น 450000" required>
                                    <span class="input-group-text">บาท</span>
                                    <div class="invalid-feedback" id="error_price"></div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" id="btnSaveCar">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="saveSpinner"></span>
                            <i class="bi bi-check-circle-fill me-1" id="saveIcon"></i>
                            <span id="saveButtonText">บันทึกข้อมูล</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: ดูรายละเอียดรถยนต์ (View Car Details Modal) -->
    <!-- ======================================================== -->
    <div class="modal fade" id="viewCarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-info-circle me-2 text-primary"></i>รายละเอียดรถยนต์
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center pb-3 border-bottom mb-3">
                        <span class="badge mb-2 fs-6" id="viewCarBadge">-</span>
                        <h4 class="fw-bold text-dark mb-1" id="viewCarTitle">-</h4>
                        <p class="text-muted small mb-0" id="viewCarCode">-</p>
                    </div>

                    <div class="row g-2 small">
                        <div class="col-6 text-muted">ยี่ห้อ:</div>
                        <div class="col-6 fw-bold text-end" id="viewCarBrand">-</div>

                        <div class="col-6 text-muted">รุ่น:</div>
                        <div class="col-6 fw-bold text-end" id="viewCarModel">-</div>

                        <div class="col-6 text-muted">ปี ค.ศ.:</div>
                        <div class="col-6 fw-bold text-end" id="viewCarYear">-</div>

                        <div class="col-6 text-muted">สี:</div>
                        <div class="col-6 fw-bold text-end" id="viewCarColor">-</div>

                        <div class="col-6 text-muted">ทะเบียนรถ:</div>
                        <div class="col-6 fw-bold text-end" id="viewCarPlate">-</div>

                        <div class="col-6 text-muted">ราคารถยนต์:</div>
                        <div class="col-6 fw-bold text-end text-primary fs-5" id="viewCarPrice">-</div>
                    </div>


                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: ยืนยันการลบ (Delete Confirmation Modal) -->
    <!-- ======================================================== -->
    <div class="modal fade" id="deleteCarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow text-center p-3">
                <div class="modal-body pt-4">
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex p-3 fs-1 mb-3">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark">ยืนยันการลบรถยนต์?</h5>
                    <p class="text-secondary small mb-3">
                        คุณแน่ใจหรือไม่ว่าต้องการลบรถรหัส <strong id="deleteCarCodeText" class="text-danger">-</strong>
                        ออกจากระบบอย่างถาวร?
                    </p>
                    <input type="hidden" id="deleteCarId">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3"
                            data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-danger btn-sm px-3 fw-semibold" id="btnConfirmDelete">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="deleteSpinner"></span>
                            <span>ยืนยันลบ</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // ตั้งค่า CSRF Token สำหรับทุก AJAX Request
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ฟังก์ชันดึงหรือสร้าง Bootstrap Modal อย่างปลอดภัย
            function getModal(modalId) {
                const el = document.getElementById(modalId);
                if (!el) return null;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    return bootstrap.Modal.getOrCreateInstance(el);
                }
                return {
                    show: function() {
                        $(el).modal('show');
                    },
                    hide: function() {
                        $(el).modal('hide');
                    }
                };
            }

            let currentViewPrice = 0;

            // 1. โหลดข้อมูลรายการรถยนต์ทั้งหมด (Read via AJAX)
            function loadCars() {
                $('#tableLoading').removeClass('d-none');

                let keyword = $('#filterKeyword').val().trim();
                let status = $('#filterStatus').val();
                let brand = $('#filterBrand').val();

                $.ajax({
                    url: "{{ route('cars.index') }}",
                    type: "GET",
                    data: {
                        json: 1,
                        keyword: keyword,
                        status: status,
                        brand: brand
                    },
                    dataType: "json",
                    success: function(res) {
                        $('#tableLoading').addClass('d-none');
                        if (res.success) {
                            renderTable(res.data);
                            updateKpis(res.summary);
                        }
                    },
                    error: function(xhr) {
                        $('#tableLoading').addClass('d-none');
                        showToast('error', 'ไม่สามารถโหลดข้อมูลรถยนต์ได้ กรุณาลองใหม่อีกครั้ง');
                    }
                });
            }

            // 2. เรนเดอร์แถวตารางข้อมูลรถยนต์ (Render DOM)
            function renderTable(cars) {
                let tbody = $('#carsTableBody');
                tbody.empty();

                $('#recordsCountBadge').text(`ทั้งหมด ${cars.length} คัน`);

                if (cars.length === 0) {
                    tbody.html(`
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-car-front fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <h6 class="fw-bold mb-1">ไม่พบข้อมูลรถยนต์ในระบบ</h6>
                            <p class="small text-muted mb-0">ลองเปลี่ยนคำค้นหา หรือกดปุ่ม <strong>"เพิ่มรถใหม่"</strong> ด้านบน</p>
                        </td>
                    </tr>
                `);
                    return;
                }

                cars.forEach(function(car) {
                    let badgeClass = car.status_badge || 'bg-secondary';
                    let label = car.status_label || car.status;
                    let yearText = car.model_year ? car.model_year : '-';
                    let colorText = car.color ? car.color : '-';
                    let plateText = car.license_plate ?
                        `<span class="badge bg-light text-dark border">${car.license_plate}</span>` : '-';

                    let row = `
                    <tr id="car-row-${car.id}">
                        <td class="fw-bold text-primary">
                            <i class="bi bi-car-front me-1"></i>${car.car_code}
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">${car.brand}</div>
                            <div class="text-muted small">${car.model}</div>
                        </td>
                        <td class="text-center">${yearText}</td>
                        <td>${colorText}</td>
                        <td>${plateText}</td>
                        <td class="text-end fw-bold text-dark">${car.formatted_price}</td>
                        <td class="text-center">
                            <span class="badge ${badgeClass}">${label}</span>
                        </td>
                        <td class="text-center">
                            <div class="action-btn-group btn-group" role="group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-view-car" data-id="${car.id}" title="ดูรายละเอียด">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm btn-edit-car" data-id="${car.id}" title="แก้ไข">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete-car" data-id="${car.id}" data-code="${car.car_code}" title="ลบ">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                    tbody.append(row);
                });
            }

            // 3. อัปเดตสถิติ KPI Cards
            function updateKpis(summary) {
                if (!summary) return;
                $('#kpiTotal').text(summary.total || 0);
                $('#kpiAvailable').text(summary.available || 0);
                $('#kpiReserved').text(summary.reserved || 0);
                $('#kpiSold').text(summary.sold || 0);
            }

            // 4. ค้นหา Realtime ด้วย Debounce
            let searchTimer;
            $('#filterKeyword').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    loadCars();
                }, 300);
            });

            // กรองตามสถานะและยี่ห้อ
            $('#filterStatus, #filterBrand').on('change', function() {
                loadCars();
            });

            // ปุ่มรีเซ็ตตัวกรอง
            $('#btnResetFilters').on('click', function() {
                $('#filterKeyword').val('');
                $('#filterStatus').val('all');
                $('#filterBrand').val('all');
                loadCars();
            });

            // 5. เปิด Modal เพิ่มรถใหม่ (Create Mode)
            $('#btnOpenAddModal').on('click', function() {
                resetForm();
                $('#carId').val('');
                $('#carModalTitle').html('<i class="bi bi-plus-circle me-2"></i>เพิ่มข้อมูลรถยนต์ใหม่');
                $('#saveButtonText').text('บันทึกข้อมูล');
                getModal('carModal').show();
            });

            // 6. เปิด Modal แก้ไขข้อมูล (Update Mode)
            $(document).on('click', '.btn-edit-car', function() {
                resetForm();
                let carId = $(this).data('id');

                $.ajax({
                    url: `/cars/${carId}/edit`,
                    type: "GET",
                    dataType: "json",
                    success: function(res) {
                        if (res.success && res.data) {
                            let car = res.data;
                            $('#carId').val(car.id);
                            $('#car_code').val(car.car_code);
                            $('#brand').val(car.brand);
                            $('#model').val(car.model);
                            $('#model_year').val(car.model_year || '');
                            $('#color').val(car.color || '');
                            $('#license_plate').val(car.license_plate || '');
                            $('#price').val(car.price);
                            $('#status').val(car.status);

                            $('#carModalTitle').html(
                                '<i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูลรถยนต์ (' +
                                car.car_code + ')');
                            $('#saveButtonText').text('อัปเดตข้อมูล');
                            getModal('carModal').show();
                        }
                    },
                    error: function() {
                        showToast('error', 'ไม่สามารถดึงข้อมูลรถยนต์ได้');
                    }
                });
            });

            // 7. บันทึกข้อมูล (Store หรือ Update ผ่าน AJAX)
            $('#carForm').on('submit', function(e) {
                e.preventDefault();
                clearValidationErrors();

                let carId = $('#carId').val();
                let isEdit = carId !== '';
                let url = isEdit ? `/cars/${carId}` : "{{ route('cars.store') }}";
                let method = isEdit ? 'PUT' : 'POST';

                let formData = {
                    id: carId,
                    car_code: $('#car_code').val().trim(),
                    brand: $('#brand').val().trim(),
                    model: $('#model').val().trim(),
                    model_year: $('#model_year').val(),
                    color: $('#color').val().trim(),
                    license_plate: $('#license_plate').val().trim(),
                    price: $('#price').val(),
                    status: $('#status').val(),
                    _method: method
                };

                // Loading state บนปุ่มบันทึก
                $('#btnSaveCar').prop('disabled', true);
                $('#saveSpinner').removeClass('d-none');
                $('#saveIcon').addClass('d-none');

                $.ajax({
                    url: url,
                    type: "POST", // ส่งเป็น POST พร้อม _method เสมอสำหรับ Laravel Form
                    data: formData,
                    dataType: "json",
                    success: function(res) {
                        $('#btnSaveCar').prop('disabled', false);
                        $('#saveSpinner').addClass('d-none');
                        $('#saveIcon').removeClass('d-none');

                        if (res.success) {
                            getModal('carModal').hide();
                            showToast('success', res.message);
                            loadCars(); // อัปเดตตารางทันทีโดยไม่รีเฟรชหน้า
                        }
                    },
                    error: function(xhr) {
                        $('#btnSaveCar').prop('disabled', false);
                        $('#saveSpinner').addClass('d-none');
                        $('#saveIcon').removeClass('d-none');

                        if (xhr.status === 422) {
                            // แสดงข้อผิดพลาด Validation ใต้ฟิลด์
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                $(`#${field}`).addClass('is-invalid');
                                $(`#error_${field}`).text(errors[field][0]).show();
                            }
                        } else {
                            showToast('error',
                                'เกิดข้อผิดพลาดจากเซิร์ฟเวอร์ กรุณาลองใหม่อีกครั้ง');
                        }
                    }
                });
            });

            // 8. ดูรายละเอียดรถยนต์ (Show Modal via AJAX)
            $(document).on('click', '.btn-view-car', function() {
                let carId = $(this).data('id');

                $.ajax({
                    url: `/cars/${carId}`,
                    type: "GET",
                    dataType: "json",
                    success: function(res) {
                        if (res.success && res.data) {
                            let car = res.data;
                            currentViewPrice = car.price;

                            $('#viewCarTitle').text(`${car.brand} ${car.model}`);
                            $('#viewCarCode').text(`รหัส: ${car.car_code}`);
                            $('#viewCarBrand').text(car.brand);
                            $('#viewCarModel').text(car.model);
                            $('#viewCarYear').text(car.model_year ? car.model_year : 'ไม่ระบุ');
                            $('#viewCarColor').text(car.color ? car.color : 'ไม่ระบุ');
                            $('#viewCarPlate').text(car.license_plate ? car.license_plate :
                                'ไม่มีทะเบียน');
                            $('#viewCarPrice').text(`${car.formatted_price} บาท`);

                            let badgeEl = $('#viewCarBadge');
                            badgeEl.attr('class', `badge mb-2 fs-6 ${car.status_badge}`);
                            badgeEl.text(car.status_label);

                            getModal('viewCarModal').show();
                        }
                    },
                    error: function() {
                        showToast('error', 'ไม่สามารถดึงข้อมูลรถยนต์ได้');
                    }
                });
            });



            // 9. ลบข้อมูลรถยนต์ (Delete via AJAX)
            $(document).on('click', '.btn-delete-car', function() {
                let carId = $(this).data('id');
                let carCode = $(this).data('code');

                $('#deleteCarId').val(carId);
                $('#deleteCarCodeText').text(carCode);
                getModal('deleteCarModal').show();
            });

            $('#btnConfirmDelete').on('click', function() {
                let carId = $('#deleteCarId').val();

                $('#btnConfirmDelete').prop('disabled', true);
                $('#deleteSpinner').removeClass('d-none');

                $.ajax({
                    url: `/cars/${carId}`,
                    type: "DELETE",
                    dataType: "json",
                    success: function(res) {
                        $('#btnConfirmDelete').prop('disabled', false);
                        $('#deleteSpinner').addClass('d-none');
                        getModal('deleteCarModal').hide();

                        if (res.success) {
                            showToast('success', res.message);
                            loadCars(); // อัปเดตตารางทันทีโดยไม่รีเฟรชหน้า
                        }
                    },
                    error: function() {
                        $('#btnConfirmDelete').prop('disabled', false);
                        $('#deleteSpinner').addClass('d-none');
                        showToast('error', 'ไม่สามารถลบข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
                    }
                });
            });

            // Helper: ล้างข้อมูลฟอร์ม
            function resetForm() {
                $('#carForm')[0].reset();
                clearValidationErrors();
            }

            // Helper: ล้าง Error Validation
            function clearValidationErrors() {
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('').hide();
            }

            // Helper: แสดง Toast Notification
            function showToast(type, message) {
                let alertClass = (type === 'success') ? 'alert-success' : 'alert-danger';
                let icon = (type === 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';

                let alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                    <i class="bi ${icon} fs-5"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

                $('#ajaxAlertContainer').html(alertHtml);

                // Auto dismiss after 4 seconds
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 4000);
            }

            // เริ่มต้นโหลดข้อมูลทันทีเมื่อเปิดหน้าเว็บ
            loadCars();
        });
    </script>
@endsection
