<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chookiat Leasing - ระบบจัดการคลังรถและคำนวณค่างวด')</title>

    <!-- Google Fonts: Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5.3.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Prompt', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .navbar-brand-custom {
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #0d6efd !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .badge-role-admin {
            background-color: #dc3545;
            color: #fff;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .badge-role-user {
            background-color: #0d6efd;
            color: #fff;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .main-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            background: #ffffff;
        }
    </style>

    @yield('styles')
</head>

<body>
    @auth
        <!-- Navbar for Authenticated Users -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-bold" href="{{ route('dashboard') }}">
                    <i class="bi bi-car-front-fill text-primary fs-4"></i>
                    <span>Chookiat <span class="text-primary">Leasing</span></span>
                </a>

                <!-- User Info & Logout Button (Always visible on all screen sizes) -->
                <div class="d-flex align-items-center gap-3 ms-auto order-lg-last">
                    <div class="text-white text-end d-none d-sm-block">
                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <small class="text-secondary">@ {{ Auth::user()->username }}</small>
                            @if (Auth::user()->isAdmin())
                                <span class="badge badge-role-admin">Admin</span>
                            @else
                                <span class="badge badge-role-user">User</span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>ออกจากระบบ</span>
                        </button>
                    </form>
                </div>

                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cars.*') ? 'active' : '' }}" href="{{ route('cars.index') }}">
                                <i class="bi bi-car-front-fill me-1"></i> จัดการคลังรถ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('installments.*') ? 'active' : '' }}" href="{{ route('installments.index') }}">
                                <i class="bi bi-calculator me-1"></i> คำนวณค่างวด
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    @endauth

    <!-- Flash Messages -->
    <div class="container mt-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main>
        @yield('content')
    </main>

    @yield('scripts')
</body>

</html>
