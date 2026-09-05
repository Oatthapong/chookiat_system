@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน | Chookiat Leasing')

@section('content')
    <div class="container py-4">
        <!-- Header Section -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-primary"></i> จัดการผู้ใช้งาน (User Management)
                </h3>
                <p class="text-secondary mb-0">
                    จัดการสิทธิ์การใช้งาน, รีเซ็ตข้อมูลผู้ใช้ และระงับ/ยกเลิกการเข้าใช้งานระบบ
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> กลับสู่ Dashboard
            </a>
        </div>

        <!-- Alert Container for AJAX notifications -->
        <div id="ajaxAlertBox" class="d-none alert alert-dismissible fade show mb-4" role="alert">
            <span id="ajaxAlertMsg"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card main-card border-0 shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 fs-4 d-flex align-items-center justify-content-center"
                            style="width: 52px; height: 52px;">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">ผู้ใช้งานทั้งหมด</div>
                            <h4 class="fw-bold mb-0 text-dark" id="kpiTotal">{{ $kpi['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card main-card border-0 shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 fs-4 d-flex align-items-center justify-content-center"
                            style="width: 52px; height: 52px;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">พร้อมใช้งาน (Active)</div>
                            <h4 class="fw-bold mb-0 text-success" id="kpiActive">{{ $kpi['active'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card main-card border-0 shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 fs-4 d-flex align-items-center justify-content-center"
                            style="width: 52px; height: 52px;">
                            <i class="bi bi-slash-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">ระงับการใช้งาน (Inactive)</div>
                            <h4 class="fw-bold mb-0 text-danger" id="kpiInactive">{{ $kpi['inactive'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card main-card border-0 shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-dark bg-opacity-10 text-dark p-3 fs-4 d-flex align-items-center justify-content-center"
                            style="width: 52px; height: 52px;">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">ผู้ดูแลระบบ (Admin)</div>
                            <h4 class="fw-bold mb-0 text-dark" id="kpiAdmin">{{ $kpi['admin'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card main-card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0"
                                placeholder="ค้นหาชื่อ-นามสกุล, Username หรือ อีเมล..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="statusFilter" class="form-select">
                            <option value="">-- สถานะทั้งหมด --</option>
                            <option value="active">พร้อมใช้งาน (Active)</option>
                            <option value="inactive">ระงับการใช้งาน (Inactive)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="roleFilter" class="form-select">
                            <option value="">-- ระดับสิทธิ์ทั้งหมด --</option>
                            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                            <option value="user">ผู้ใช้ทั่วไป (User)</option>
                        </select>
                    </div>
                    <div class="col-md-1 text-end">
                        <button id="btnResetFilter" class="btn btn-outline-secondary w-100" title="ล้างตัวกรอง">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="card main-card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 ps-4" style="width: 70px;">ID</th>
                                <th class="py-3">ชื่อ-นามสกุล</th>
                                <th class="py-3">Username</th>
                                <th class="py-3">อีเมล</th>
                                <th class="py-3 text-center" style="width: 130px;">ระดับสิทธิ์</th>
                                <th class="py-3 text-center" style="width: 150px;">สถานะการเข้าใช้งาน</th>
                                <th class="py-3 text-center pe-4" style="width: 250px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            @forelse($users as $u)
                                <tr id="user-row-{{ $u->id }}">
                                    <td class="ps-4 fw-semibold text-muted">#{{ $u->id }}</td>
                                    <td>
                                        <div class="fw-bold text-dark user-name">{{ $u->name }}</div>
                                    </td>
                                    <td>
                                        <code class="text-primary fw-semibold user-username">{{ $u->username }}</code>
                                    </td>
                                    <td>
                                        <span class="text-muted user-email">{{ $u->email }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($u->isAdmin())
                                            <span class="badge bg-danger">Admin</span>
                                        @else
                                            <span class="badge bg-primary">User</span>
                                        @endif
                                    </td>
                                    <td class="text-center user-status-col">
                                        @if ($u->is_active)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                                <i class="bi bi-check-circle-fill me-1"></i> พร้อมใช้งาน
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                                <i class="bi bi-slash-circle-fill me-1"></i> ถูกระงับสิทธิ์
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- ปุ่ม Toggle Status (ยกเลิก / เปิดใช้งาน User Login) -->
                                            @if (Auth::id() === $u->id)
                                                <button class="btn btn-outline-secondary btn-sm" disabled
                                                    title="ไม่สามารถระงับบัญชีของตนเองได้">
                                                    <i class="bi bi-lock"></i> บัญชีปัจจุบัน
                                                </button>
                                            @else
                                                @if ($u->is_active)
                                                    <button class="btn btn-outline-warning btn-sm btn-toggle-status"
                                                        data-id="{{ $u->id }}" data-action="disable"
                                                        title="ยกเลิกการใช้งาน (ระงับบัญชี)">
                                                        <i class="bi bi-slash-circle text-danger me-1"></i> ระงับ Login
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-success btn-sm btn-toggle-status"
                                                        data-id="{{ $u->id }}" data-action="enable"
                                                        title="เปิดการใช้งาน">
                                                        <i class="bi bi-check-circle text-success me-1"></i> ปลดระงับ
                                                    </button>
                                                @endif
                                            @endif

                                            <!-- ปุ่ม Reset ข้อมูลผู้ใช้ -->
                                            <button class="btn btn-outline-primary btn-sm btn-edit-user"
                                                data-id="{{ $u->id }}" title="Reset/แก้ไขข้อมูลผู้ใช้">
                                                <i class="bi bi-pencil-square"></i> แก้ไข
                                            </button>

                                            <!-- ปุ่ม Reset รหัสผ่าน -->
                                            <button class="btn btn-outline-danger btn-sm btn-reset-password"
                                                data-id="{{ $u->id }}" data-username="{{ $u->username }}"
                                                data-name="{{ $u->name }}" title="รีเซ็ตรหัสผ่าน">
                                                <i class="bi bi-key-fill"></i> รหัสผ่าน
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        ไม่พบข้อมูลผู้ใช้งานในระบบ
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODAL: แก้ไข / Reset ข้อมูลผู้ใช้ ================= -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="editUserForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editUserId" name="id">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="editUserModalLabel">
                            <i class="bi bi-pencil-square me-2"></i> รีเซ็ต / แก้ไขข้อมูลผู้ใช้
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div id="editUserError" class="alert alert-danger d-none mb-3 py-2 small"></div>

                        <div class="mb-3">
                            <label for="editName" class="form-label fw-semibold">ชื่อ-นามสกุล <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editName" name="name" required
                                maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label for="editUsername" class="form-label fw-semibold">Username <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editUsername" name="username" required
                                maxlength="100">
                            <div class="form-text">ใช้สำหรับเข้าสู่ระบบ ต้องไม่ซ้ำกับผู้อื่น</div>
                        </div>

                        <div class="mb-3">
                            <label for="editEmail" class="form-label fw-semibold">อีเมล <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="editEmail" name="email" required
                                maxlength="150">
                            <div class="form-text">ใช้สำหรับกู้คืนรหัสผ่าน (Forgot Password)</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" id="btnSaveEditUser" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= MODAL: รีเซ็ตรหัสผ่าน (Reset Password) ================= -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="resetPasswordForm">
                    @csrf
                    <input type="hidden" id="resetPasswordUserId" name="id">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">
                            <i class="bi bi-key-fill me-2"></i> รีเซ็ตรหัสผ่านผู้ใช้
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div id="resetPasswordError" class="alert alert-danger d-none mb-3 py-2 small"></div>

                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-person-fill me-1"></i> กำลังรีเซ็ตรหัสผ่านให้:
                            <strong id="resetTargetUsername" class="text-dark"></strong>
                            (<span id="resetTargetName"></span>)
                        </div>

                        <div class="mb-3">
                            <label for="newPassword" class="form-label fw-semibold">รหัสผ่านใหม่ <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" name="password" required
                                    minlength="6" placeholder="อย่างน้อย 6 ตัวอักษร">
                                <button class="btn btn-outline-secondary btn-toggle-pw" type="button"
                                    data-target="#newPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="newPasswordConfirmation" class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่ <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPasswordConfirmation"
                                    name="password_confirmation" required minlength="6"
                                    placeholder="พิมพ์ซ้ำเพื่อยืนยัน">
                                <button class="btn btn-outline-secondary btn-toggle-pw" type="button"
                                    data-target="#newPasswordConfirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" id="btnQuickPassword" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-magic me-1"></i> ตั้งเป็นค่าเริ่มต้น (password123)
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" id="btnSaveResetPassword" class="btn btn-danger">
                            <i class="bi bi-shield-check me-1"></i> ยืนยันรีเซ็ตรหัสผ่าน
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Current authenticated user ID
            const currentUserId = {{ Auth::id() }};

            // Global AJAX setup with CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            // Helper: Bootstrap modal instance retriever
            function getModal(modalId) {
                const el = document.getElementById(modalId);
                if (!el) return null;
                return bootstrap.Modal.getOrCreateInstance(el);
            }

            // Helper: Show Alert
            function showAlert(msg, type = 'success') {
                const $alert = $('#ajaxAlertBox');
                $alert.removeClass('d-none alert-success alert-danger alert-warning alert-info')
                    .addClass('alert-' + type);
                $('#ajaxAlertMsg').html(msg);
                $alert[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                setTimeout(function() {
                    $alert.addClass('d-none');
                }, 5000);
            }

            // Toggle password visibility
            $('.btn-toggle-pw').on('click', function() {
                const targetId = $(this).data('target');
                const $input = $(targetId);
                const $icon = $(this).find('i');
                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    $input.attr('type', 'password');
                    $icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // Quick password default button
            $('#btnQuickPassword').on('click', function() {
                $('#newPassword').val('password123');
                $('#newPasswordConfirmation').val('password123');
            });

            // ==========================================
            // 1. AJAX Search & Filtering
            // ==========================================
            let searchTimer = null;

            function loadUsers() {
                const search = $('#searchInput').val();
                const status = $('#statusFilter').val();
                const role = $('#roleFilter').val();

                $.ajax({
                    url: "{{ route('users.index') }}",
                    type: "GET",
                    data: {
                        search: search,
                        status: status,
                        role: role
                    },
                    beforeSend: function() {
                        $('#userTableBody').css('opacity', '0.5');
                    },
                    success: function(res) {
                        $('#userTableBody').css('opacity', '1');
                        if (res.success) {
                            renderTable(res.users);
                            // Update KPIs
                            $('#kpiTotal').text(res.kpi.total);
                            $('#kpiActive').text(res.kpi.active);
                            $('#kpiInactive').text(res.kpi.inactive);
                            $('#kpiAdmin').text(res.kpi.admin);
                        }
                    },
                    error: function() {
                        $('#userTableBody').css('opacity', '1');
                        showAlert('เกิดข้อผิดพลาดในการโหลดข้อมูลผู้ใช้งาน', 'danger');
                    }
                });
            }

            $('#searchInput').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(loadUsers, 300);
            });

            $('#statusFilter, #roleFilter').on('change', function() {
                loadUsers();
            });

            $('#btnResetFilter').on('click', function() {
                $('#searchInput').val('');
                $('#statusFilter').val('');
                $('#roleFilter').val('');
                loadUsers();
            });

            function renderTable(users) {
                const $tbody = $('#userTableBody');
                $tbody.empty();

                if (!users || users.length === 0) {
                    $tbody.html(`
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        ไม่พบข้อมูลผู้ใช้งานที่ตรงกับเงื่อนไข
                    </td>
                </tr>
            `);
                    return;
                }

                users.forEach(function(u) {
                    const roleBadge = u.role === 'admin' ?
                        `<span class="badge bg-danger">Admin</span>` :
                        `<span class="badge bg-primary">User</span>`;

                    const statusBadge = u.is_active ?
                        `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                       <i class="bi bi-check-circle-fill me-1"></i> พร้อมใช้งาน
                   </span>` :
                        `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                       <i class="bi bi-slash-circle-fill me-1"></i> ถูกระงับสิทธิ์
                   </span>`;

                    let toggleBtn = '';
                    if (parseInt(u.id) === currentUserId) {
                        toggleBtn = `<button class="btn btn-outline-secondary btn-sm" disabled title="ไม่สามารถระงับบัญชีของตนเองได้">
                                <i class="bi bi-lock"></i> บัญชีปัจจุบัน
                             </button>`;
                    } else if (u.is_active) {
                        toggleBtn = `<button class="btn btn-outline-warning btn-sm btn-toggle-status" data-id="${u.id}" data-action="disable" title="ยกเลิกการใช้งาน (ระงับบัญชี)">
                                <i class="bi bi-slash-circle text-danger me-1"></i> ระงับ Login
                             </button>`;
                    } else {
                        toggleBtn = `<button class="btn btn-outline-success btn-sm btn-toggle-status" data-id="${u.id}" data-action="enable" title="เปิดการใช้งาน">
                                <i class="bi bi-check-circle text-success me-1"></i> ปลดระงับ
                             </button>`;
                    }

                    const row = `
                <tr id="user-row-${u.id}">
                    <td class="ps-4 fw-semibold text-muted">#${u.id}</td>
                    <td>
                        <div class="fw-bold text-dark user-name">${escapeHtml(u.name)}</div>
                    </td>
                    <td>
                        <code class="text-primary fw-semibold user-username">${escapeHtml(u.username)}</code>
                    </td>
                    <td>
                        <span class="text-muted user-email">${escapeHtml(u.email)}</span>
                    </td>
                    <td class="text-center">${roleBadge}</td>
                    <td class="text-center user-status-col">${statusBadge}</td>
                    <td class="text-center pe-4">
                        <div class="btn-group btn-group-sm" role="group">
                            ${toggleBtn}
                            <button class="btn btn-outline-primary btn-sm btn-edit-user" data-id="${u.id}" title="Reset/แก้ไขข้อมูลผู้ใช้">
                                <i class="bi bi-pencil-square"></i> แก้ไข
                            </button>
                            <button class="btn btn-outline-danger btn-sm btn-reset-password" data-id="${u.id}" data-username="${escapeHtml(u.username)}" data-name="${escapeHtml(u.name)}" title="รีเซ็ตรหัสผ่าน">
                                <i class="bi bi-key-fill"></i> รหัสผ่าน
                            </button>
                        </div>
                    </td>
                </tr>
            `;
                    $tbody.append(row);
                });
            }

            function escapeHtml(text) {
                if (!text) return '';
                return String(text)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // ==========================================
            // 2. Toggle Status (ยกเลิกการใช้งาน User Login)
            // ==========================================
            $(document).on('click', '.btn-toggle-status', function() {
                const userId = $(this).data('id');
                const action = $(this).data('action');
                const confirmMsg = action === 'disable' ?
                    'คุณแน่ใจหรือไม่ว่าต้องการ "ยกเลิกการใช้งาน" (ระงับบัญชี) ของผู้ใช้นี้? ผู้ใช้จะไม่สามารถ Login เข้าสู่ระบบได้' :
                    'คุณต้องการ "เปิดการใช้งาน" บัญชีนี้ให้กลับมา Login ได้ตามปกติหรือไม่?';

                if (!confirm(confirmMsg)) {
                    return;
                }

                const $btn = $(this);
                $btn.prop('disabled', true);

                $.ajax({
                    url: `/users/${userId}/toggle-status`,
                    type: 'PATCH',
                    success: function(res) {
                        if (res.success) {
                            showAlert(res.message, res.is_active ? 'success' : 'warning');
                            loadUsers();
                        } else {
                            showAlert(res.message || 'เกิดข้อผิดพลาด', 'danger');
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false);
                        const errMsg = xhr.responseJSON?.message ||
                            'เกิดข้อผิดพลาดในการเปลี่ยนสถานะการใช้งาน';
                        showAlert(errMsg, 'danger');
                    }
                });
            });

            // ==========================================
            // 3. Reset ข้อมูลผู้ใช้ (Edit User Info)
            // ==========================================
            $(document).on('click', '.btn-edit-user', function() {
                const userId = $(this).data('id');
                $('#editUserError').addClass('d-none').html('');
                $('#editUserForm')[0].reset();

                $.ajax({
                    url: `/users/${userId}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success) {
                            const u = res.user;
                            $('#editUserId').val(u.id);
                            $('#editName').val(u.name);
                            $('#editUsername').val(u.username);
                            $('#editEmail').val(u.email);

                            getModal('editUserModal').show();
                        }
                    },
                    error: function() {
                        showAlert('ไม่สามารถดึงข้อมูลผู้ใช้งานได้', 'danger');
                    }
                });
            });

            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();
                const userId = $('#editUserId').val();
                const $btn = $('#btnSaveEditUser');
                const originalText = $btn.html();

                $('#editUserError').addClass('d-none').html('');
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> กำลังบันทึก...');

                $.ajax({
                    url: `/users/${userId}`,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(res) {
                        $btn.prop('disabled', false).html(originalText);
                        if (res.success) {
                            getModal('editUserModal').hide();
                            showAlert(res.message, 'success');
                            loadUsers();
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalText);
                        let errorsHtml = '';
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const errs = xhr.responseJSON.errors;
                            errorsHtml = '<ul class="mb-0 ps-3">';
                            for (let key in errs) {
                                errorsHtml += `<li>${errs[key][0]}</li>`;
                            }
                            errorsHtml += '</ul>';
                        } else {
                            errorsHtml = xhr.responseJSON?.message ||
                                'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                        }
                        $('#editUserError').html(errorsHtml).removeClass('d-none');
                    }
                });
            });

            // ==========================================
            // 4. Reset รหัสผ่าน (Reset Password)
            // ==========================================
            $(document).on('click', '.btn-reset-password', function() {
                const userId = $(this).data('id');
                const username = $(this).data('username');
                const name = $(this).data('name');

                $('#resetPasswordError').addClass('d-none').html('');
                $('#resetPasswordForm')[0].reset();
                $('#resetPasswordUserId').val(userId);
                $('#resetTargetUsername').text(username);
                $('#resetTargetName').text(name);

                getModal('resetPasswordModal').show();
            });

            $('#resetPasswordForm').on('submit', function(e) {
                e.preventDefault();
                const userId = $('#resetPasswordUserId').val();
                const $btn = $('#btnSaveResetPassword');
                const originalText = $btn.html();

                $('#resetPasswordError').addClass('d-none').html('');
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> กำลังรีเซ็ต...');

                $.ajax({
                    url: `/users/${userId}/reset-password`,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        $btn.prop('disabled', false).html(originalText);
                        if (res.success) {
                            getModal('resetPasswordModal').hide();
                            showAlert(res.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalText);
                        let errorsHtml = '';
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const errs = xhr.responseJSON.errors;
                            errorsHtml = '<ul class="mb-0 ps-3">';
                            for (let key in errs) {
                                errorsHtml += `<li>${errs[key][0]}</li>`;
                            }
                            errorsHtml += '</ul>';
                        } else {
                            errorsHtml = xhr.responseJSON?.message ||
                                'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน';
                        }
                        $('#resetPasswordError').html(errorsHtml).removeClass('d-none');
                    }
                });
            });
        });
    </script>
@endsection
