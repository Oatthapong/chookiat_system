@extends('layouts.app')

@section('title', 'คำนวณยอดผ่อนชำระ | Chookiat Leasing')

@section('styles')
<style>
    .calc-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }

    .calc-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
        border-radius: 14px 14px 0 0 !important;
        padding: 20px 24px;
    }

    .table-installments th {
        background-color: #f1f5f9;
        color: #334155;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .table-installments td {
        vertical-align: middle;
        text-align: center;
        font-size: 0.95rem;
    }

    /* Highlight แถวที่เลือก ชัดเจนใน Bootstrap 5 (กำหนดเจาะจงที่ td) */
    .table-installments tbody tr.row-highlight > td {
        background-color: #cfe2ff !important; /* สีฟ้าชัดเจน โดดเด่น */
        color: #052c65 !important;
        font-weight: 600;
        box-shadow: none !important;
        border-top: 1px solid #9ec5fe !important;
        border-bottom: 1px solid #9ec5fe !important;
    }

    /* เส้นขอบซ้ายหนา 5px เน้นแถวที่เลือก */
    .table-installments tbody tr.row-highlight > td:first-child {
        border-left: 5px solid #0d6efd !important;
    }

    .table-installments tbody tr.row-highlight > td:last-child {
        border-right: 1px solid #9ec5fe !important;
    }

    /* Hover บนแถวที่เลือกอยู่ (สีฟ้าเข้มขึ้นเด่นชัด) */
    .table-installments tbody tr.row-highlight:hover > td {
        background-color: #b6d4fe !important;
        border-color: #6ea8fe !important;
    }

    /* ยอดผ่อนเกิน 5,000 เป็นสีแดงตาม Requirement */
    .amount-over-5000,
    .table-installments tbody tr.row-highlight > td.amount-over-5000 {
        color: #dc3545 !important;
        font-weight: 800 !important;
    }

    .amount-normal {
        color: #198754;
        font-weight: 600;
    }

    .input-group-text {
        font-size: 0.9rem;
        background-color: #f8fafc;
        color: #475569;
    }

    .cursor-pointer {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }

    .cursor-pointer:hover {
        background-color: #f1f5f9 !important;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-calculator text-primary me-2"></i>ระบบคำนวณยอดผ่อนชำระ
            </h3>
            <p class="text-secondary small mb-0">
                คำนวณค่างวดเช่าซื้อรถยนต์แบบ Flat Rate พร้อมตารางผ่อนชำระ 12 – 84 งวด
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> กลับสู่ Dashboard
        </a>
    </div>

    <!-- Calculation Input Card -->
    <div class="card calc-card mb-4">
        <div class="card-header calc-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-sliders fs-5"></i>
                <h5 class="mb-0 fw-bold">ระบุข้อมูลการคำนวณ</h5>
            </div>
            <!-- ปุ่ม Clear แสดงเฉพาะเมื่อมี Input อย่างน้อย 1 ค่า -->
            <button type="button" id="clearBtn" class="btn btn-warning btn-sm fw-semibold shadow-sm d-none">
                <i class="bi bi-eraser-fill me-1"></i> ล้างข้อมูล (Clear)
            </button>
        </div>

        <div class="card-body p-4">
            <form id="calcForm" onsubmit="return false;">
                @csrf
                <div class="row g-3">
                    <!-- ยอดจัด -->
                    <div class="col-md-4">
                        <label for="principal" class="form-label fw-semibold text-secondary small">
                            ยอดจัดสินเชื่อ (Principal) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                            <input type="text"
                                   class="form-control"
                                   id="principal"
                                   name="principal"
                                   placeholder="เช่น 300000"
                                   autocomplete="off"
                                   required>
                            <span class="input-group-text">บาท</span>
                        </div>
                        <div class="form-text small text-muted">
                            *ตัวเลขจำนวนบวกเท่านั้น (ห้ามติดลบหรือใส่อักขระ)
                        </div>
                    </div>

                    <!-- อัตราดอกเบี้ย -->
                    <div class="col-md-4">
                        <label for="interest_rate" class="form-label fw-semibold text-secondary small">
                            อัตราดอกเบี้ยต่อปี (Flat Rate) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-percent"></i></span>
                            <input type="text"
                                   class="form-control"
                                   id="interest_rate"
                                   name="interest_rate"
                                   placeholder="เช่น 4.5"
                                   autocomplete="off"
                                   required>
                            <span class="input-group-text">% ต่อปี</span>
                        </div>
                        <div class="form-text small text-muted">
                            *ดอกเบี้ยคงที่ต่อปี (เช่น 3.5, 4.0, 5.0)
                        </div>
                    </div>

                    <!-- จำนวนงวดที่เลือก -->
                    <div class="col-md-4">
                        <label for="installments" class="form-label fw-semibold text-secondary small">
                            จำนวนงวดที่ต้องการ (Terms) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            <select class="form-select" id="installments" name="installments">
                                <option value="" selected>-- เลือกจำนวนงวด (เช่น 48 งวด) --</option>
                                <option value="12">12 งวด (1 ปี)</option>
                                <option value="18">18 งวด (1.5 ปี)</option>
                                <option value="24">24 งวด (2 ปี)</option>
                                <option value="30">30 งวด (2.5 ปี)</option>
                                <option value="36">36 งวด (3 ปี)</option>
                                <option value="42">42 งวด (3.5 ปี)</option>
                                <option value="48">48 งวด (4 ปี)</option>
                                <option value="54">54 งวด (4.5 ปี)</option>
                                <option value="60">60 งวด (5 ปี)</option>
                                <option value="66">66 งวด (5.5 ปี)</option>
                                <option value="72">72 งวด (6 ปี)</option>
                                <option value="78">78 งวด (6.5 ปี)</option>
                                <option value="84">84 งวด (7 ปี)</option>
                            </select>
                        </div>
                        <div class="form-text small text-muted">
                            *ตารางจะทำการ Highlight งวดที่เลือกนี้
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" id="calcBtn" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-play-fill fs-5"></i>
                        <span>คำนวณค่างวด</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Box (แสดงผลลัพธ์ของงวดที่เลือก) -->
    <div id="summaryCard" class="card calc-card mb-4 border-0 bg-light d-none">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-3 text-center border-end">
                    <span class="text-muted small text-uppercase fw-bold">ยอดจัดสินเชื่อ</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1" id="sumPrincipal">0 บาท</h4>
                </div>
                <div class="col-md-3 text-center border-end">
                    <span class="text-muted small text-uppercase fw-bold">ดอกเบี้ยรวม (<span id="sumYears">0</span> ปี)</span>
                    <h4 class="fw-bold text-info mb-0 mt-1" id="sumInterest">0 บาท</h4>
                </div>
                <div class="col-md-3 text-center border-end">
                    <span class="text-muted small text-uppercase fw-bold">ยอดรวมทั้งหมด</span>
                    <h4 class="fw-bold text-primary mb-0 mt-1" id="sumTotal">0 บาท</h4>
                </div>
                <div class="col-md-3 text-center">
                    <span class="text-muted small text-uppercase fw-bold">ค่างวดต่อเดือน (<span id="sumMonths">0</span> งวด)</span>
                    <h3 class="fw-bold mb-0 mt-1" id="sumMonthly">0 บาท</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Installment Table Result Card -->
    <div class="card calc-card">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-table text-primary me-2"></i>ตารางเปรียบเทียบยอดผ่อนชำระ (12 – 84 งวด)
            </h5>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 small">
                    <i class="bi bi-circle-fill fs-6 me-1"></i> สีแดง = ค่างวดมากกว่า 5,000 บาท
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 small">
                    <i class="bi bi-check-circle-fill me-1"></i> ไฮไลต์ = งวดที่เลือก
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-installments mb-0" id="installmentsTable">
                    <thead>
                        <tr>
                            <th style="width: 12%;">จำนวนงวด</th>
                            <th style="width: 12%;">ระยะเวลา</th>
                            <th style="width: 18%;">ค่างวดต่อเดือน (ไม่รวม VAT)</th>
                            <th style="width: 15%;">VAT 7%</th>
                            <th style="width: 18%;">ค่างวดรวม VAT / เดือน</th>
                            <th style="width: 15%;">ดอกเบี้ยรวม</th>
                            <th style="width: 10%;">เลือก</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-info-circle fs-3 d-block mb-2 text-secondary"></i>
                                กรุณาระบุยอดจัดและอัตราดอกเบี้ย แล้วกด <strong>"คำนวณค่างวด"</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        const terms = [12, 18, 24, 30, 36, 42, 48, 54, 60, 66, 72, 78, 84];

        // 1. Requirement 2.5 Validation ยอดจัด:
        // - ยอดจัดต้องเป็นตัวเลขเท่านั้น
        // - ถ้าเป็นอักขระให้ Alert ทันทีและ Reset เป็นค่าว่าง
        // - ถ้าติดลบให้ Alert ทันทีและ Reset เป็นค่าว่าง
        $('#principal').on('input change', function () {
            let val = $(this).val();
            if (val === '') {
                toggleClearButton();
                return;
            }

            // ตรวจสอบค่าติดลบ
            if (val.includes('-') || parseFloat(val) < 0) {
                alert('ยอดจัดสินเชื่อต้องไม่ติดลบ กรุณากรอกใหม่อีกครั้ง');
                $(this).val('');
                toggleClearButton();
                return;
            }

            // ตรวจสอบว่าเป็นอักขระที่ไม่ใช่ตัวเลข (อนุญาตเฉพาะตัวเลข 0-9 และจุดทศนิยม .)
            if (/[^\d.]/.test(val) || (val.match(/\./g) || []).length > 1) {
                alert('ยอดจัดต้องเป็นตัวเลขเท่านั้น กรุณากรอกใหม่อีกครั้ง');
                $(this).val('');
                toggleClearButton();
                return;
            }

            toggleClearButton();
        });

        // ตรวจสอบอัตราดอกเบี้ย
        $('#interest_rate').on('input change', function () {
            let val = $(this).val();
            if (val === '') {
                toggleClearButton();
                return;
            }

            if (val.includes('-') || parseFloat(val) < 0) {
                alert('อัตราดอกเบี้ยต้องไม่ติดลบ');
                $(this).val('');
                toggleClearButton();
                return;
            }

            if (/[^\d.]/.test(val) || (val.match(/\./g) || []).length > 1) {
                alert('อัตราดอกเบี้ยต้องเป็นตัวเลขเท่านั้น');
                $(this).val('');
                toggleClearButton();
                return;
            }

            toggleClearButton();
        });

        // 2. Requirement 2.5 ปุ่ม Clear:
        // - แสดงเฉพาะเมื่อ Input ตามข้อคำนวณมีค่าอย่างน้อยหนึ่งค่า
        function toggleClearButton() {
            let p = $('#principal').val().trim();
            let r = $('#interest_rate').val().trim();
            let i = $('#installments').val();

            if (p !== '' || r !== '' || (i !== '' && i !== null)) {
                $('#clearBtn').removeClass('d-none').show();
            } else {
                $('#clearBtn').addClass('d-none').hide();
            }
        }

        $('#installments').on('change', function () {
            toggleClearButton();
            // ถ้างวดเปลี่ยนและตารางเรนเดอร์อยู่แล้ว ให้ Highlight แถวทันที
            if ($('#tableBody tr td').length > 1) {
                calculateAndRenderTable();
            }
        });

        // กดปุ่ม Clear -> ล้างข้อมูลทั้งหมด
        $('#clearBtn').on('click', function () {
            $('#principal').val('');
            $('#interest_rate').val('');
            $('#installments').val(''); // ล้างจำนวนงวด
            toggleClearButton();

            $('#summaryCard').addClass('d-none');
            $('#tableBody').html(`
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-info-circle fs-3 d-block mb-2 text-secondary"></i>
                        กรุณาระบุยอดจัดและอัตราดอกเบี้ย แล้วกด <strong>"คำนวณค่างวด"</strong>
                    </td>
                </tr>
            `);
        });

        // 3. ฟังก์ชันคำนวณและเรนเดอร์ตาราง (Client-side Flat Rate + Server AJAX รองรับ)
        function calculateAndRenderTable() {
            let principal = parseFloat($('#principal').val());
            let interestRate = parseFloat($('#interest_rate').val());
            let selectedMonths = parseInt($('#installments').val()) || 48;

            if (isNaN(principal) || principal <= 0) {
                alert('กรุณากรอกยอดจัดให้ถูกต้อง');
                $('#principal').focus();
                return;
            }

            if (isNaN(interestRate) || interestRate < 0) {
                alert('กรุณากรอกอัตราดอกเบี้ยให้ถูกต้อง');
                $('#interest_rate').focus();
                return;
            }

            let html = '';
            let selectedSummary = null;

            terms.forEach(function (months) {
                let years = months / 12;
                let totalInterest = principal * (interestRate / 100) * years;
                let totalAmount = principal + totalInterest;
                let monthly = totalAmount / months;
                let vat = monthly * 0.07;
                let monthlyWithVat = monthly + vat;

                let isSelected = (months === selectedMonths);
                let isOver5000 = (monthly > 5000);

                if (isSelected) {
                    selectedSummary = {
                        months: months,
                        years: years.toFixed(1),
                        monthly: monthly,
                        monthlyWithVat: monthlyWithVat,
                        totalInterest: totalInterest,
                        totalAmount: totalAmount
                    };
                }

                // CSS Highlight row & Amount > 5000 is red
                let rowClass = isSelected ? 'table-primary row-highlight cursor-pointer' : 'cursor-pointer';
                let amountClass = isOver5000 ? 'amount-over-5000' : 'amount-normal';

                html += `
                    <tr class="${rowClass}" data-months="${months}">
                        <td class="fw-bold">
                            ${months} งวด
                            ${isSelected ? '<span class="badge bg-primary ms-1">เลือกอยู่</span>' : ''}
                        </td>
                        <td>${years.toFixed(1)} ปี</td>
                        <td class="${amountClass}">
                            ${numberFormat(monthly)} บาท
                        </td>
                        <td class="text-muted">${numberFormat(vat)} บาท</td>
                        <td class="fw-bold text-dark">${numberFormat(monthlyWithVat)} บาท</td>
                        <td class="text-secondary">${numberFormat(totalInterest)} บาท</td>
                        <td>
                            <button type="button" class="btn btn-sm ${isSelected ? 'btn-primary' : 'btn-outline-primary'} select-row-btn" data-months="${months}">
                                ${isSelected ? '<i class="bi bi-check-lg"></i>' : 'เลือก'}
                            </button>
                        </td>
                    </tr>
                `;
            });

            $('#tableBody').html(html);

            // อัปเดต Summary Box
            if (selectedSummary) {
                $('#sumPrincipal').text(numberFormat(principal) + ' บาท');
                $('#sumYears').text(selectedSummary.years);
                $('#sumMonths').text(selectedSummary.months);
                $('#sumInterest').text(numberFormat(selectedSummary.totalInterest) + ' บาท');
                $('#sumTotal').text(numberFormat(selectedSummary.totalAmount) + ' บาท');

                let monthlyEl = $('#sumMonthly');
                monthlyEl.text(numberFormat(selectedSummary.monthly) + ' บาท');
                if (selectedSummary.monthly > 5000) {
                    monthlyEl.removeClass('text-success text-dark').addClass('text-danger');
                } else {
                    monthlyEl.removeClass('text-danger text-dark').addClass('text-success');
                }

                $('#summaryCard').removeClass('d-none');
            }
        }

        // กดปุ่มคำนวณ
        $('#calcBtn').on('click', function () {
            calculateAndRenderTable();
        });

        // เมื่อคลิกที่แถวในตารางเพื่อเปลี่ยนงวดที่เลือก
        $(document).on('click', '#installmentsTable tbody tr', function (e) {
            let months = $(this).data('months');
            if (months) {
                $('#installments').val(months);
                calculateAndRenderTable();
            }
        });

        // Helper จัดรูปแบบตัวเลขทศนิยม 2 ตำแหน่ง พร้อม comma
        function numberFormat(number) {
            return Number(number).toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    });
</script>
@endsection
