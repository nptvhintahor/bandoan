<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin') | FoodShop Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @yield('head')
    <style>
        :root {
            --brand:     #E8501A;
            --brand-dk:  #C03E0E;
            --brand-lt:  #FFF1EB;
            --ink:       #1C1008;
            --ink-2:     #5A3E2A;
            --ink-3:     #9A7860;
            --surface:   #F8F5F2;
            --card:      #FFFFFF;
            --border:    #EDE8E3;
            --sidebar:   #13100C;
            --sidebar-w: 256px;
            --green:     #16A34A;
            --green-lt:  #DCFCE7;
            --blue:      #1D6FBE;
            --blue-lt:   #DBEAFE;
            --amber:     #B45309;
            --amber-lt:  #FEF3C7;
            --red-lt:    #FEE2E2;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--ink);
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 50;
        }

        .sidebar-logo {
            padding: 26px 20px 22px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            text-decoration: none;
        }
        .logo-box {
            width: 38px; height: 38px;
            background: var(--brand);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 14px rgba(232,80,26,0.45);
            flex-shrink: 0;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            color: #fff;
            font-weight: 700;
            letter-spacing: -0.2px;
        }
        .logo-text em { color: #FFA882; font-style: normal; }
        .logo-sub {
            font-size: 10px;
            color: rgba(255,255,255,0.28);
            font-weight: 500;
            letter-spacing: 1px;
            margin-top: 1px;
        }

        .sidebar-section { padding: 20px 12px 6px; }
        .sidebar-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.22);
            padding: 0 10px;
            margin-bottom: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            color: rgba(255,255,255,0.48);
            text-decoration: none;
            transition: all 0.18s;
            margin-bottom: 2px;
        }
        .nav-item i { width: 16px; text-align: center; font-size: 13px; }
        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.85);
        }
        .nav-item.active {
            background: rgba(232,80,26,0.18);
            color: #FFA882;
        }
        .nav-item.active i { color: var(--brand); }
        .nav-badge {
            margin-left: auto;
            background: var(--brand);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 100px;
            min-width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 12px 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.04);
        }
        .user-avatar {
            width: 32px; height: 32px;
            background: var(--brand);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.75); }
        .user-role { font-size: 11px; color: rgba(255,255,255,0.3); margin-top: 1px; }

        /* ===== MAIN ===== */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            height: 64px;
            background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .topbar-left h1 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.3px;
        }
        .topbar-left p { font-size: 12.5px; color: var(--ink-3); margin-top: 1px; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-btn {
            width: 36px; height: 36px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--card);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--ink-3);
            font-size: 13px;
            transition: all 0.18s;
            text-decoration: none;
            position: relative;
        }
        .topbar-btn:hover { border-color: var(--brand); color: var(--brand); background: var(--brand-lt); }
        .notif-dot {
            width: 7px; height: 7px;
            background: var(--brand);
            border-radius: 50%;
            position: absolute;
            top: 6px; right: 6px;
            border: 1.5px solid white;
        }
        .topbar-date {
            font-size: 12.5px;
            color: var(--ink-3);
            background: var(--surface);
            padding: 7px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ===== CONTENT ===== */
        .content { padding: 28px 32px 48px; flex: 1; }

        /* scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #DDD8D0; border-radius: 3px; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <a href="/admin/dashboard" class="sidebar-logo">
        <div class="logo-box">🍔</div>
        <div>
            <div class="logo-text">Food<em>Shop</em></div>
            <div class="logo-sub">Admin Panel</div>
        </div>
    </a>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Tổng quan</div>
        <a href="/admin/dashboard" class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>
        <a href="/admin/orders" class="nav-item {{ Request::is('admin/orders') || Request::is('admin') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i> Đơn hàng
            @if(isset($pendingCount) && $pendingCount > 0)
                <span class="nav-badge">{{ $pendingCount }}</span>
            @endif
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Thực đơn</div>
        <a href="/admin/foods" class="nav-item {{ Request::is('admin/foods') ? 'active' : '' }}">
            <i class="fas fa-hamburger"></i> Quản lý món ăn
        </a>
        <a href="/admin/foods/create" class="nav-item {{ Request::is('admin/foods/create') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> Thêm món mới
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Hệ thống</div>
        <a href="/" class="nav-item">
            <i class="fas fa-external-link-alt"></i> Xem trang chủ
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar">A</div>
            <div>
                <div class="user-name">Admin</div>
                <div class="user-role">Super Admin</div>
            </div>
            <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,0.3);font-size:13px;text-decoration:none;transition:color 0.2s"
               onmouseover="this.style.color='#FFA882'" onmouseout="this.style.color='rgba(255,255,255,0.3)'">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</aside>

<!-- MAIN -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">
            <h1>@yield('page-title', 'Dashboard')</h1>
            <p>@yield('page-sub', '')</p>
        </div>
        <div class="topbar-right">
            <div class="topbar-date">
                <i class="far fa-calendar-alt"></i>
                <span id="today-date">--/--/----</span>
            </div>
            <a href="/admin/orders" class="topbar-btn" title="Thông báo">
                <i class="fas fa-bell"></i>
                <span class="notif-dot"></span>
            </a>
            <a href="/" class="topbar-btn" title="Xem site">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">
        @yield('content')
    </div>

</div>

<script>
    document.getElementById('today-date').textContent = new Date().toLocaleDateString('vi-VN');
</script>
@yield('scripts')
</body>
</html>