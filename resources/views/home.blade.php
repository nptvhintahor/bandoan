<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>FoodShop — Đặt đồ ăn online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --brand:     #E8501A;
            --brand-dk:  #C03E0E;
            --brand-lt:  #FFF1EB;
            --ink:       #1C1008;
            --ink-mid:   #6B4C34;
            --ink-lt:    #A07858;
            --surface:   #FFFAF7;
            --card:      #FFFFFF;
            --border:    rgba(232,80,26,0.12);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--ink);
            overflow-x: hidden;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--surface); }
        ::-webkit-scrollbar-thumb { background: #E8C0A8; border-radius: 3px; }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 0 40px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            background: rgba(255,250,247,0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            transition: box-shadow 0.3s;
        }
        .navbar.scrolled {
            box-shadow: 0 4px 32px rgba(232,80,26,0.08);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .nav-logo-icon {
            width: 36px; height: 36px;
            background: var(--brand);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(232,80,26,0.3);
        }
        .nav-logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.3px;
        }
        .nav-logo-text span { color: var(--brand); }

        /* ===== NAV SEARCH ===== */
        .nav-search-wrap {
            flex: 1;
            max-width: 340px;
            position: relative;
        }
        .nav-search-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .nav-search-icon {
            position: absolute;
            left: 14px;
            color: var(--ink-lt);
            pointer-events: none;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .nav-search-input {
            width: 100%;
            padding: 9px 36px 9px 40px;
            border-radius: 100px;
            border: 1.5px solid var(--border);
            background: var(--card);
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            color: var(--ink);
            outline: none;
            transition: all 0.25s;
            box-shadow: 0 2px 8px rgba(232,80,26,0.05);
        }
        .nav-search-input::placeholder { color: var(--ink-lt); }
        .nav-search-input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(232,80,26,0.1), 0 2px 8px rgba(232,80,26,0.08);
        }
        .nav-search-input:focus + .nav-search-icon,
        .nav-search-input-wrap:focus-within .nav-search-icon {
            color: var(--brand);
        }
        .nav-search-clear {
            position: absolute;
            right: 12px;
            display: none;
            align-items: center;
            justify-content: center;
            width: 20px; height: 20px;
            background: rgba(160,120,88,0.15);
            border-radius: 50%;
            cursor: pointer;
            border: none;
            color: var(--ink-lt);
            transition: all 0.2s;
        }
        .nav-search-clear:hover { background: rgba(232,80,26,0.12); color: var(--brand); }
        .nav-search-clear.visible { display: flex; }

        /* Dropdown results */
        .nav-search-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0; right: 0;
            background: var(--card);
            border: 1px solid rgba(232,80,26,0.15);
            border-radius: 16px;
            box-shadow: 0 16px 48px rgba(28,16,8,0.14);
            overflow: hidden;
            display: none;
            z-index: 200;
            max-height: 400px;
            overflow-y: auto;
        }
        .nav-search-dropdown.open { display: block; }
        .nav-search-dropdown::-webkit-scrollbar { width: 4px; }
        .nav-search-dropdown::-webkit-scrollbar-thumb { background: #E8C0A8; border-radius: 2px; }

        .search-dropdown-header {
            padding: 10px 16px 6px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--ink-lt);
        }
        .search-result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
            border-top: 1px solid rgba(232,80,26,0.05);
        }
        .search-result-item:first-of-type { border-top: none; }
        .search-result-item:hover { background: var(--brand-lt); }
        .search-result-img {
            width: 44px; height: 44px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
            border: 1px solid rgba(232,80,26,0.1);
        }
        .search-result-info { flex: 1; min-width: 0; }
        .search-result-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }
        .search-result-name mark {
            background: none;
            color: var(--brand);
            font-weight: 700;
        }
        .search-result-price {
            font-size: 12px;
            color: var(--brand);
            font-weight: 600;
            font-family: 'Playfair Display', serif;
        }
        .search-result-price .original {
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            color: var(--ink-lt);
            text-decoration: line-through;
            margin-right: 5px;
            font-weight: 400;
        }
        .search-result-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 100px;
            flex-shrink: 0;
        }
        .search-result-badge.hot { background: rgba(232,80,26,0.1); color: var(--brand); }
        .search-result-badge.sale { background: rgba(22,163,74,0.1); color: #16A34A; }
        .search-result-badge.normal { background: rgba(160,120,88,0.1); color: var(--ink-mid); }

        .search-no-result {
            padding: 28px 16px;
            text-align: center;
            color: var(--ink-lt);
            font-size: 13.5px;
        }
        .search-no-result-icon { font-size: 32px; margin-bottom: 8px; }
        .search-footer {
            padding: 10px 16px;
            border-top: 1px solid rgba(232,80,26,0.07);
            font-size: 12px;
            color: var(--ink-lt);
            text-align: center;
            background: var(--surface);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-shrink: 0;
        }
        .nav-links a {
            font-size: 14px;
            font-weight: 500;
            color: var(--ink-mid);
            text-decoration: none;
            transition: color 0.2s;
            letter-spacing: 0.2px;
        }
        .nav-links a:hover { color: var(--brand); }

        .nav-cart {
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--brand-lt);
            padding: 8px 14px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 600;
            color: var(--brand);
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid rgba(232,80,26,0.18);
        }
        .nav-cart:hover {
            background: var(--brand);
            color: white;
            border-color: var(--brand);
        }
        .cart-badge {
            background: var(--brand);
            color: white;
            font-size: 11px;
            font-weight: 700;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .nav-cart:hover .cart-badge {
            background: white;
            color: var(--brand);
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 100px;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        .nav-user:hover { border-color: var(--brand); color: var(--brand); }

        .nav-avatar {
            width: 28px; height: 28px;
            background: var(--brand);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-size: 13px;
            font-weight: 700;
        }

        .btn-logout {
            font-size: 13px;
            color: var(--ink-lt);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-logout:hover { color: var(--brand); background: var(--brand-lt); }

        .btn-auth {
            font-size: 14px;
            font-weight: 600;
            color: white;
            background: var(--brand);
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 100px;
            transition: all 0.2s;
            box-shadow: 0 3px 12px rgba(232,80,26,0.3);
        }
        .btn-auth:hover { background: var(--brand-dk); transform: translateY(-1px); }

        /* ===== NAV CHAT BUTTON ===== */
        .nav-chat-btn {
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            padding: 8px 14px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 600;
            color: var(--ink-mid);
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid var(--border);
        }
        .nav-chat-btn:hover {
            background: var(--brand-lt);
            color: var(--brand);
            border-color: rgba(232,80,26,0.3);
        }
        .chat-badge {
            background: var(--brand);
            color: white;
            font-size: 11px;
            font-weight: 700;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        /* ===== TOAST NOTIFICATION ===== */
        .toast {
            position: fixed;
            top: 86px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 200;
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            animation: slideDown 0.4s ease, fadeOut 0.4s ease 3s forwards;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateX(-50%) translateY(-12px); }
            to   { opacity: 1; transform: translateX(-50%) translateY(0); }
        }
        @keyframes fadeOut {
            to { opacity: 0; pointer-events: none; }
        }

        /* ===== HERO ===== */
        .hero {
            position: relative;
            height: 100vh;
            min-height: 640px;
            display: flex;
            align-items: center;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1600891964599-f61ba0e24092?w=1600&q=80');
            background-size: cover;
            background-position: center;
            transform: scale(1.04);
            animation: heroZoom 12s ease-in-out infinite alternate;
        }
        @keyframes heroZoom {
            from { transform: scale(1.04); }
            to   { transform: scale(1.10); }
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                105deg,
                rgba(28,16,8,0.82) 0%,
                rgba(28,16,8,0.55) 50%,
                rgba(28,16,8,0.2) 100%
            );
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 680px;
            padding: 0 64px;
            animation: heroFadeIn 0.9s ease both;
        }
        @keyframes heroFadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(232,80,26,0.18);
            border: 1px solid rgba(232,80,26,0.4);
            color: #FFA882;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 100px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .hero-badge-dot {
            width: 6px; height: 6px;
            background: var(--brand);
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.6; transform: scale(0.7); }
        }
        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(40px, 5.5vw, 72px);
            font-weight: 800;
            color: #fff;
            line-height: 1.08;
            letter-spacing: -1px;
            margin-bottom: 20px;
        }
        .hero h1 em {
            font-style: italic;
            color: #FFA882;
        }
        .hero-sub {
            font-size: 17px;
            color: rgba(255,255,255,0.72);
            line-height: 1.65;
            margin-bottom: 40px;
            font-weight: 300;
        }
        .hero-actions {
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--brand);
            color: white;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 30px;
            border-radius: 100px;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 6px 24px rgba(232,80,26,0.4);
            letter-spacing: 0.2px;
        }
        .btn-primary:hover {
            background: var(--brand-dk);
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba(232,80,26,0.5);
        }
        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            color: white;
            font-size: 15px;
            font-weight: 500;
            padding: 14px 28px;
            border-radius: 100px;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.25);
            transition: all 0.25s;
            backdrop-filter: blur(8px);
        }
        .btn-ghost:hover {
            background: rgba(255,255,255,0.22);
            border-color: rgba(255,255,255,0.4);
        }

        /* Hero stats */
        .hero-stats {
            position: absolute;
            bottom: 48px;
            left: 64px;
            right: 64px;
            z-index: 2;
            display: flex;
            gap: 40px;
            animation: heroFadeIn 0.9s 0.3s ease both;
        }
        .hero-stat-item {
            display: flex;
            flex-direction: column;
        }
        .hero-stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 800;
            color: white;
            line-height: 1;
        }
        .hero-stat-label {
            font-size: 12px;
            color: rgba(255,255,255,0.55);
            margin-top: 4px;
            letter-spacing: 0.3px;
        }
        .hero-stat-divider {
            width: 1px;
            background: rgba(255,255,255,0.2);
            align-self: stretch;
        }

        /* ===== SECTION COMMON ===== */
        .section {
            padding: 96px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--brand);
            margin-bottom: 12px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 48px;
        }

        /* ===== FEATURES STRIP ===== */
        .features-strip {
            background: var(--ink);
            padding: 0;
        }
        .features-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }
        .feature-item {
            padding: 36px 28px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-right: 1px solid rgba(255,255,255,0.07);
            transition: background 0.2s;
        }
        .feature-item:last-child { border-right: none; }
        .feature-item:hover { background: rgba(255,255,255,0.04); }
        .feature-icon {
            width: 44px; height: 44px;
            background: rgba(232,80,26,0.15);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .feature-title {
            font-size: 14px;
            font-weight: 600;
            color: white;
            margin-bottom: 2px;
        }
        .feature-desc {
            font-size: 12px;
            color: rgba(255,255,255,0.45);
        }

        /* ===== PROMOTIONS SECTION ===== */
        .promo-section {
            background: linear-gradient(160deg, #1C1008 0%, #2D1A0C 50%, #1C1008 100%);
            position: relative;
            overflow: hidden;
        }
        .promo-section::before {
            content: '';
            position: absolute;
            top: -80px; left: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(232,80,26,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .promo-section::after {
            content: '';
            position: absolute;
            bottom: -60px; right: -60px;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(22,163,74,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Tab switcher */
        .promo-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 40px;
            background: rgba(255,255,255,0.05);
            padding: 5px;
            border-radius: 14px;
            width: fit-content;
        }
        .promo-tab {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            background: transparent;
            color: rgba(255,255,255,0.45);
            font-family: 'DM Sans', sans-serif;
            transition: all 0.25s;
            letter-spacing: 0.2px;
        }
        .promo-tab:hover {
            color: rgba(255,255,255,0.75);
            background: rgba(255,255,255,0.06);
        }
        .promo-tab.active-sale {
            background: linear-gradient(135deg, #16A34A, #0F6E56);
            color: white;
            box-shadow: 0 4px 16px rgba(22,163,74,0.35);
        }
        .promo-tab.active-hot {
            background: linear-gradient(135deg, #E8501A, #C03E0E);
            color: white;
            box-shadow: 0 4px 16px rgba(232,80,26,0.4);
        }
        .promo-tab-count {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 100px;
            background: rgba(255,255,255,0.2);
            line-height: 1.4;
        }

        /* Promo grid */
        .promo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            transition: opacity 0.3s ease;
        }
        .promo-grid.fading { opacity: 0; }

        /* Promo card */
        .promo-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
            position: relative;
        }
        .promo-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.07);
        }
        .promo-card.sale-card:hover { box-shadow: 0 16px 40px rgba(22,163,74,0.18); border-color: rgba(22,163,74,0.3); }
        .promo-card.hot-card:hover  { box-shadow: 0 16px 40px rgba(232,80,26,0.2);  border-color: rgba(232,80,26,0.35); }

        .promo-img-wrap {
            position: relative;
            height: 180px;
            overflow: hidden;
        }
        .promo-img-wrap img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
            filter: brightness(0.85);
        }
        .promo-card:hover .promo-img-wrap img {
            transform: scale(1.07);
            filter: brightness(0.95);
        }

        /* Ribbon giảm giá */
        .promo-ribbon {
            position: absolute;
            top: 14px; right: -8px;
            padding: 5px 16px 5px 12px;
            font-size: 12px;
            font-weight: 800;
            color: white;
            border-radius: 4px 0 0 4px;
            letter-spacing: 0.5px;
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 8px 50%);
        }
        .promo-ribbon.sale { background: linear-gradient(135deg, #16A34A, #0F6E56); box-shadow: -3px 3px 12px rgba(22,163,74,0.4); }
        .promo-ribbon.hot  { background: linear-gradient(135deg, #E8501A, #C03E0E); box-shadow: -3px 3px 12px rgba(232,80,26,0.45); }

        .promo-body { padding: 18px 18px 16px; }
        .promo-name {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
            line-height: 1.25;
        }
        .promo-desc {
            font-size: 12.5px;
            color: rgba(255,255,255,0.45);
            line-height: 1.5;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .promo-footer {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding-top: 14px;
            border-top: 1px solid rgba(255,255,255,0.07);
            gap: 10px;
        }
        .promo-price-wrap { display: flex; flex-direction: column; gap: 3px; }
        .promo-price-old {
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            text-decoration: line-through;
            font-family: 'DM Sans', sans-serif;
        }
        .promo-price-new {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
        }
        .promo-price-new.sale { color: #4ADE80; }
        .promo-price-new.hot  { color: #FFA882; }
        .promo-price-new span {
            font-size: 12px;
            font-weight: 400;
            font-family: 'DM Sans', sans-serif;
            color: rgba(255,255,255,0.4);
            margin-left: 2px;
        }

        .promo-pct-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 4px;
            width: fit-content;
        }
        .promo-pct-badge.sale { background: rgba(22,163,74,0.2); color: #4ADE80; border: 1px solid rgba(22,163,74,0.25); }
        .promo-pct-badge.hot  { background: rgba(232,80,26,0.2); color: #FFA882; border: 1px solid rgba(232,80,26,0.25); }

        .btn-promo-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            padding: 9px 16px;
            border-radius: 100px;
            border: none;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: all 0.22s;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .btn-promo-add.sale {
            background: linear-gradient(135deg, #16A34A, #0F6E56);
            color: white;
            box-shadow: 0 3px 12px rgba(22,163,74,0.3);
        }
        .btn-promo-add.sale:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(22,163,74,0.4); }
        .btn-promo-add.hot {
            background: linear-gradient(135deg, #E8501A, #C03E0E);
            color: white;
            box-shadow: 0 3px 12px rgba(232,80,26,0.3);
        }
        .btn-promo-add.hot:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(232,80,26,0.4); }
        .btn-promo-add.gray {
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.55);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .btn-promo-add.gray:hover { background: rgba(255,255,255,0.12); }

        .promo-empty {
            grid-column: 1/-1;
            text-align: center;
            padding: 60px 0;
            color: rgba(255,255,255,0.35);
            font-size: 15px;
        }
        .promo-empty-icon { font-size: 44px; margin-bottom: 14px; }

        /* Section title dark variant */
        .section-title-dark {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 8px;
        }
        .section-eyebrow-dark {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #FFA882;
            margin-bottom: 12px;
        }
        .section-sub-dark {
            font-size: 15px;
            color: rgba(255,255,255,0.45);
            margin-bottom: 36px;
            line-height: 1.6;
        }

        /* ===== MENU GRID ===== */
        .menu-section {
            background: var(--surface);
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
            gap: 24px;
        }

        .food-card {
            background: var(--card);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
            position: relative;
        }
        .food-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 48px rgba(232,80,26,0.12);
            border-color: rgba(232,80,26,0.25);
        }

        .food-img-wrap {
            position: relative;
            overflow: hidden;
            height: 200px;
        }
        .food-img-wrap img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .food-card:hover .food-img-wrap img {
            transform: scale(1.07);
        }
        .food-img-badge {
            position: absolute;
            top: 14px; left: 14px;
            background: var(--brand);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 100px;
            letter-spacing: 0.5px;
        }
        .food-img-badge--hot {
            background: linear-gradient(135deg, #E8501A, #C03E0E);
            box-shadow: 0 2px 8px rgba(232,80,26,0.4);
            animation: hotPulse 2s ease-in-out infinite;
        }
        @keyframes hotPulse {
            0%,100% { box-shadow: 0 2px 8px rgba(232,80,26,0.4); }
            50%      { box-shadow: 0 2px 16px rgba(232,80,26,0.7); }
        }
        .food-img-badge--sale {
            background: linear-gradient(135deg, #16A34A, #0F6E56);
            box-shadow: 0 2px 8px rgba(22,163,74,0.35);
        }

        .food-body { padding: 20px 20px 18px; }
        .food-name {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
            line-height: 1.25;
        }
        .food-desc {
            font-size: 13px;
            color: var(--ink-lt);
            line-height: 1.55;
            margin-bottom: 18px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .food-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid rgba(232,80,26,0.08);
        }
        .food-price-block {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .food-price-original {
            font-size: 13px;
            font-weight: 400;
            color: var(--ink-lt);
            text-decoration: line-through;
            font-family: 'DM Sans', sans-serif;
            line-height: 1;
        }
        .food-price-original span { font-size: 11px; margin-left: 1px; }
        .food-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--brand);
            font-family: 'Playfair Display', serif;
        }
        .food-price span {
            font-size: 12px;
            font-weight: 400;
            font-family: 'DM Sans', sans-serif;
            color: var(--ink-lt);
            margin-left: 2px;
        }
        .food-price--hot  { color: #C03E0E !important; }
        .food-price--sale { color: #16A34A !important; }
        .food-discount-badge {
            display: inline-block;
            background: #ECFDF5;
            color: #065F46;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            border: 1px solid #A7F3D0;
            letter-spacing: 0.3px;
            width: fit-content;
            margin-top: 2px;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--brand);
            color: white;
            font-size: 13px;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: 100px;
            border: none;
            cursor: pointer;
            transition: all 0.22s;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            box-shadow: 0 3px 10px rgba(232,80,26,0.25);
        }
        .btn-add:hover {
            background: var(--brand-dk);
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(232,80,26,0.35);
        }
        .btn-add:active { transform: scale(0.97); }
        .btn-add-gray {
            background: #F3EDE8;
            color: var(--ink-mid);
            box-shadow: none;
        }
        .btn-add-gray:hover {
            background: #E8DDD5;
            transform: translateY(-1px);
            box-shadow: none;
        }

        .food-empty {
            grid-column: 1/-1;
            text-align: center;
            padding: 80px 0;
        }
        .food-empty-icon { font-size: 48px; margin-bottom: 16px; }
        .food-empty p {
            font-size: 15px;
            color: var(--ink-lt);
        }

        /* ===== CTA BAND ===== */
        .cta-band {
            background: var(--ink);
            padding: 80px 40px;
        }
        .cta-inner {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        .cta-inner h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 3.5vw, 44px);
            font-weight: 800;
            color: white;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }
        .cta-inner h2 em { font-style: italic; color: #FFA882; }
        .cta-inner p {
            font-size: 16px;
            color: rgba(255,255,255,0.55);
            margin-bottom: 36px;
            line-height: 1.6;
        }

        /* ===== FOOTER ===== */
        footer {
            background: #120A04;
            padding: 60px 40px 36px;
        }
        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
        }
        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 48px;
            padding-bottom: 40px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            margin-bottom: 28px;
            flex-wrap: wrap;
        }
        .footer-brand p {
            font-size: 13px;
            color: rgba(255,255,255,0.38);
            margin-top: 12px;
            max-width: 260px;
            line-height: 1.65;
        }
        .footer-links h4 {
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            margin-bottom: 14px;
            font-weight: 600;
        }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a {
            font-size: 13.5px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-links a:hover { color: #FFA882; }
        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .footer-copy {
            font-size: 12.5px;
            color: rgba(255,255,255,0.28);
        }
        .footer-copy a { color: var(--brand); text-decoration: none; }

        /* ===== REVEAL ANIMATION ===== */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; gap: 10px; }
            .nav-search-wrap { max-width: 160px; }
            .hero-content { padding: 0 24px; }
            .hero-stats { left: 24px; right: 24px; gap: 20px; }
            .hero-stat-num { font-size: 22px; }
            .section { padding: 64px 24px; }
            .features-inner { grid-template-columns: 1fr 1fr; }
            .feature-item:nth-child(2) { border-right: none; }
            .nav-links { gap: 10px; }
            .footer-top { flex-direction: column; gap: 32px; }
            .cta-band { padding: 60px 24px; }
            footer { padding: 48px 24px 28px; }
            .promo-tabs { flex-wrap: wrap; }
        }
        @media (max-width: 900px) {
            .nav-search-wrap { max-width: 200px; }
        }
    </style>
    <link rel="stylesheet" href="/chatbox.css">
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar" id="navbar">
    <a href="/" class="nav-logo">
        <div class="nav-logo-icon">🍔</div>
        <span class="nav-logo-text">Food<span>Shop</span></span>
    </a>

    {{-- ===== THANH TÌM KIẾM ===== --}}
    <div class="nav-search-wrap" id="navSearchWrap">
        <div class="nav-search-input-wrap">
            <svg class="nav-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
                type="text"
                class="nav-search-input"
                id="navSearchInput"
                placeholder="Tìm món ăn..."
                autocomplete="off"
                spellcheck="false"
            >
            <button class="nav-search-clear" id="navSearchClear" title="Xóa">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="nav-search-dropdown" id="navSearchDropdown">
            <div class="search-dropdown-header" id="searchDropdownHeader">Kết quả tìm kiếm</div>
            <div id="searchResultsList"></div>
            <div class="search-footer" id="searchFooter" style="display:none"></div>
        </div>
    </div>

    <div class="nav-links">
        <a href="/">Trang chủ</a>
        <a href="#menu">Menu</a>
        <a href="#khuyen-mai">Khuyến mãi</a>

        {{-- Nút nhắn tin — chỉ hiện khi đã đăng nhập --}}
        @if(session('user'))
        <a href="/chat" class="nav-chat-btn" id="navChatBtn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            Nhắn tin
            <div class="chat-badge" id="chatBadge" style="display:none">0</div>
        </a>
        @endif

        {{-- Giỏ hàng --}}
        <a href="/cart" class="nav-cart">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            Giỏ hàng
            <div class="cart-badge">{{ count(session('cart', [])) }}</div>
        </a>

        {{-- Auth state --}}
        @if(session('user'))
            <a href="/profile" class="nav-user">
                <div class="nav-avatar">{{ strtoupper(substr(session('user')['name'], 0, 1)) }}</div>
                {{ session('user')['name'] }}
            </a>
            <a href="/logout" class="btn-logout">Đăng xuất</a>
        @else
            <a href="/auth" class="btn-auth">Đăng nhập</a>
        @endif
    </div>
</nav>

<!-- ===== TOAST SUCCESS ===== -->
@if(session('success'))
<div class="toast">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    {{ session('success') }}
</div>
@endif

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <div class="hero-badge">
            <div class="hero-badge-dot"></div>
            Giao hàng trong 30 phút
        </div>

        <h1>Đồ ăn <em>ngon</em><br>giao tận nơi</h1>

        <p class="hero-sub">
            Hàng trăm món ăn từ các nhà hàng uy tín.<br>
            Nhanh chóng · Tiện lợi · Giá tốt nhất.
        </p>

        <div class="hero-actions">
            <a href="#menu" class="btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                Đặt món ngay
            </a>
            <a href="#khuyen-mai" class="btn-ghost">
                Xem khuyến mãi
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
    </div>

    <div class="hero-stats">
        <div class="hero-stat-item">
            <div class="hero-stat-num">500+</div>
            <div class="hero-stat-label">Món ăn</div>
        </div>
        <div class="hero-stat-divider"></div>
        <div class="hero-stat-item">
            <div class="hero-stat-num">30'</div>
            <div class="hero-stat-label">Giao hàng</div>
        </div>
        <div class="hero-stat-divider"></div>
        <div class="hero-stat-item">
            <div class="hero-stat-num">10K+</div>
            <div class="hero-stat-label">Khách hàng</div>
        </div>
        <div class="hero-stat-divider"></div>
        <div class="hero-stat-item">
            <div class="hero-stat-num">4.9★</div>
            <div class="hero-stat-label">Đánh giá</div>
        </div>
    </div>
</section>

<!-- ===== FEATURES STRIP ===== -->
<div class="features-strip">
    <div class="features-inner">
        <div class="feature-item">
            <div class="feature-icon">⚡</div>
            <div>
                <div class="feature-title">Giao siêu tốc</div>
                <div class="feature-desc">Trong vòng 30 phút</div>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🍽️</div>
            <div>
                <div class="feature-title">Món tươi ngon</div>
                <div class="feature-desc">Chế biến đúng giờ</div>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🔒</div>
            <div>
                <div class="feature-title">Thanh toán an toàn</div>
                <div class="feature-desc">Bảo mật 100%</div>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🎁</div>
            <div>
                <div class="feature-title">Ưu đãi hàng ngày</div>
                <div class="feature-desc">Voucher & combo hot</div>
            </div>
        </div>
    </div>
</div>

<!-- ===== PROMOTIONS SECTION ===== -->
@php
    $saleFoods = $foods->filter(fn($f) => !is_null($f->voucher_price) && $f->voucher_price < $f->price)->values();
    $hotFoods  = $foods->filter(fn($f) => !is_null($f->voucher_price) && $f->voucher_price > $f->price)->values();
    $hasPromos = $saleFoods->count() > 0 || $hotFoods->count() > 0;
@endphp

@if($hasPromos)
<div class="promo-section" id="khuyen-mai">
    <div class="section" style="position:relative;z-index:1">

        <p class="section-eyebrow-dark reveal">Ưu đãi đặc biệt</p>
        <h2 class="section-title-dark reveal" style="transition-delay:0.08s">
            Khuyến mãi <em style="font-style:italic;color:#FFA882">hôm nay</em>
        </h2>
        <p class="section-sub-dark reveal" style="transition-delay:0.14s">
            Những món ăn đang có giá đặc biệt — giảm giá và combo hấp dẫn chỉ trong hôm nay.
        </p>

        {{-- Tabs --}}
        <div class="promo-tabs reveal" style="transition-delay:0.18s" id="promoTabs">
            @if($saleFoods->count() > 0)
            <button class="promo-tab active-sale" onclick="switchPromoTab('sale', this)" data-tab="sale">
                🏷️ Giảm giá
                <span class="promo-tab-count">{{ $saleFoods->count() }}</span>
            </button>
            @endif
            @if($hotFoods->count() > 0)
            <button class="promo-tab {{ $saleFoods->count() === 0 ? 'active-hot' : '' }}" onclick="switchPromoTab('hot', this)" data-tab="hot">
                🔥 Tăng giá HOT
                <span class="promo-tab-count">{{ $hotFoods->count() }}</span>
            </button>
            @endif
        </div>

        {{-- Sale grid --}}
        <div class="promo-grid" id="promoGridSale" style="{{ $saleFoods->count() === 0 ? 'display:none' : '' }}">
            @forelse($saleFoods as $i => $food)
            @php
                $discountPct = round((1 - $food->voucher_price / $food->price) * 100);
            @endphp
            <div class="promo-card sale-card reveal" style="transition-delay: {{ $i * 0.06 }}s">
                <div class="promo-img-wrap">
                    <img src="{{ asset($food->image) }}"
                         onerror="this.src='https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&q=80'"
                         alt="{{ $food->name }}" loading="lazy">
                    <div class="promo-ribbon sale">-{{ $discountPct }}% OFF</div>
                </div>
                <div class="promo-body">
                    <h3 class="promo-name">{{ $food->name }}</h3>
                    <p class="promo-desc">{{ $food->description }}</p>
                    <div class="promo-footer">
                        <div class="promo-price-wrap">
                            <div class="promo-price-old">{{ number_format($food->price) }}đ</div>
                            <div class="promo-price-new sale">{{ number_format($food->voucher_price) }}<span>đ</span></div>
                            <div class="promo-pct-badge sale">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                                Tiết kiệm {{ number_format($food->price - $food->voucher_price) }}đ
                            </div>
                        </div>
                        @if(session('user'))
                            <form action="/cart/add/{{ $food->id }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-promo-add sale">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Thêm giỏ
                                </button>
                            </form>
                        @else
                            <a href="/auth" class="btn-promo-add gray">Đăng nhập</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="promo-empty"><div class="promo-empty-icon">🏷️</div><p>Chưa có món giảm giá</p></div>
            @endforelse
        </div>

        {{-- Hot grid --}}
        <div class="promo-grid" id="promoGridHot" style="{{ $saleFoods->count() > 0 ? 'display:none' : '' }}">
            @forelse($hotFoods as $i => $food)
            @php
                $hotPct = round(($food->voucher_price / $food->price - 1) * 100);
            @endphp
            <div class="promo-card hot-card reveal" style="transition-delay: {{ $i * 0.06 }}s">
                <div class="promo-img-wrap">
                    <img src="{{ asset($food->image) }}"
                         onerror="this.src='https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&q=80'"
                         alt="{{ $food->name }}" loading="lazy">
                    <div class="promo-ribbon hot">🔥 HOT +{{ $hotPct }}%</div>
                </div>
                <div class="promo-body">
                    <h3 class="promo-name">{{ $food->name }}</h3>
                    <p class="promo-desc">{{ $food->description }}</p>
                    <div class="promo-footer">
                        <div class="promo-price-wrap">
                            <div class="promo-price-old">{{ number_format($food->price) }}đ</div>
                            <div class="promo-price-new hot">{{ number_format($food->voucher_price) }}<span>đ</span></div>
                            <div class="promo-pct-badge hot">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                                Combo đặc biệt
                            </div>
                        </div>
                        @if(session('user'))
                            <form action="/cart/add/{{ $food->id }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-promo-add hot">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Thêm giỏ
                                </button>
                            </form>
                        @else
                            <a href="/auth" class="btn-promo-add gray">Đăng nhập</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="promo-empty"><div class="promo-empty-icon">🔥</div><p>Chưa có món HOT</p></div>
            @endforelse
        </div>

    </div>
</div>
@endif

<!-- ===== MENU SECTION ===== -->
<div class="menu-section" id="menu">
    <div class="section">
        <p class="section-eyebrow reveal">Thực đơn hôm nay</p>
        <h2 class="section-title reveal" style="transition-delay:0.1s">Menu <em style="font-style:italic;color:var(--brand)">nổi bật</em></h2>

        <div class="menu-grid">
            @forelse($foods as $food)
            @php
                $hasVoucher   = !is_null($food->voucher_price);
                $isHot        = $hasVoucher && $food->voucher_price > $food->price;
                $isSale       = $hasVoucher && $food->voucher_price < $food->price;
                $displayPrice = $hasVoucher ? $food->voucher_price : $food->price;
            @endphp
            <div class="food-card reveal" style="transition-delay: {{ $loop->index * 0.07 }}s">

                <div class="food-img-wrap">
                    <img src="{{ asset($food->image) }}"
                         onerror="this.src='https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&q=80'"
                         alt="{{ $food->name }}"
                         loading="lazy">

                    @if($isHot)
                        <div class="food-img-badge food-img-badge--hot">🔥 HOT</div>
                    @elseif($isSale)
                        <div class="food-img-badge food-img-badge--sale">🏷️ Ưu đãi</div>
                    @else
                        <div class="food-img-badge">Nổi bật</div>
                    @endif
                </div>

                <div class="food-body">
                    <h3 class="food-name">{{ $food->name }}</h3>
                    <p class="food-desc">{{ $food->description }}</p>

                    <div class="food-footer">
                        <div class="food-price-block">
                            @if($hasVoucher)
                                <div class="food-price-original">
                                    {{ number_format($food->price) }}<span>đ</span>
                                </div>
                            @endif

                            <div class="food-price {{ $isHot ? 'food-price--hot' : ($isSale ? 'food-price--sale' : '') }}">
                                {{ number_format($displayPrice) }}<span>đ</span>
                            </div>

                            @if($isSale)
                                @php
                                    $discountPct = round((1 - $food->voucher_price / $food->price) * 100);
                                @endphp
                                <div class="food-discount-badge">-{{ $discountPct }}%</div>
                            @endif
                        </div>

                        @if(session('user'))
                            <form action="/cart/add/{{ $food->id }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-add">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Thêm vào giỏ
                                </button>
                            </form>
                        @else
                            <a href="/auth" class="btn-add btn-add-gray">
                                Đăng nhập để mua
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
                <div class="food-empty reveal">
                    <div class="food-empty-icon">🍽️</div>
                    <p>Chưa có món ăn nào. Quay lại sau nhé!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ===== CTA BAND ===== -->
<div class="cta-band">
    <div class="cta-inner reveal">
        <h2>Đói rồi? Đặt món <em>ngay thôi!</em></h2>
        <p>Hàng trăm món ăn ngon đang chờ bạn. Giao hàng nhanh chóng, giá cả hợp lý.</p>
        <a href="#menu" class="btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
            Xem toàn bộ menu
        </a>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer>
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="/" class="nav-logo" style="display:inline-flex">
                    <div class="nav-logo-icon" style="width:32px;height:32px;font-size:16px">🍔</div>
                    <span class="nav-logo-text" style="color:white">Food<span>Shop</span></span>
                </a>
                <p>Đặt đồ ăn online nhanh chóng, tiện lợi. Giao hàng tận nơi trong 30 phút.</p>
            </div>

            <div class="footer-links">
                <h4>Khám phá</h4>
                <ul>
                    <li><a href="/">Trang chủ</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#khuyen-mai">Khuyến mãi</a></li>
                    <li><a href="#">Về chúng tôi</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Tài khoản</h4>
                <ul>
                    <li><a href="/auth">Đăng nhập</a></li>
                    <li><a href="/auth">Đăng ký</a></li>
                    <li><a href="/profile">Hồ sơ</a></li>
                    <li><a href="/cart">Giỏ hàng</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Hỗ trợ</h4>
                <ul>
                    <li><a href="#">Liên hệ</a></li>
                    <li><a href="#">Chính sách</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copy">© 2026 <a href="/">FoodShop</a> — Laravel Project. All rights reserved.</p>
            <p class="footer-copy">Làm với ❤️ tại Việt Nam</p>
        </div>
    </div>
</footer>

@php
    $foodJson = $foods->map(fn($f) => [
        'id'            => $f->id,
        'name'          => $f->name,
        'description'   => $f->description ?? '',
        'image'         => asset($f->image),
        'price'         => $f->price,
        'voucher_price' => $f->voucher_price,
        'is_hot'        => !is_null($f->voucher_price) && $f->voucher_price > $f->price,
        'is_sale'       => !is_null($f->voucher_price) && $f->voucher_price < $f->price,
    ])->values();
@endphp

<script>
// ── Dữ liệu món ăn để tìm kiếm (inject từ Laravel) ──
const FOOD_DATA = @json($foodJson);

// ── Navbar scroll effect ──
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 30);
});

// ── Reveal on scroll ──
const reveals = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.12 });
reveals.forEach(el => observer.observe(el));

// ── Auto dismiss toast ──
const toast = document.querySelector('.toast');
if (toast) setTimeout(() => toast.remove(), 4000);

// ── Promo tab switcher ──
function switchPromoTab(tab, btn) {
    document.querySelectorAll('.promo-tab').forEach(t => {
        t.classList.remove('active-sale', 'active-hot');
    });
    btn.classList.add(tab === 'sale' ? 'active-sale' : 'active-hot');

    const saleGrid = document.getElementById('promoGridSale');
    const hotGrid  = document.getElementById('promoGridHot');
    const current  = tab === 'sale' ? hotGrid  : saleGrid;
    const next     = tab === 'sale' ? saleGrid : hotGrid;

    if (current && current.style.display !== 'none') {
        current.classList.add('fading');
        setTimeout(() => {
            current.style.display = 'none';
            current.classList.remove('fading');
            if (next) {
                next.style.display = 'grid';
                next.querySelectorAll('.reveal:not(.visible)').forEach(el => {
                    setTimeout(() => el.classList.add('visible'), 50);
                });
            }
        }, 250);
    } else {
        if (current) current.style.display = 'none';
        if (next) {
            next.style.display = 'grid';
            next.querySelectorAll('.reveal:not(.visible)').forEach(el => {
                setTimeout(() => el.classList.add('visible'), 50);
            });
        }
    }
}

// ── THANH TÌM KIẾM ──
(function() {
    const input       = document.getElementById('navSearchInput');
    const dropdown    = document.getElementById('navSearchDropdown');
    const resultsList = document.getElementById('searchResultsList');
    const clearBtn    = document.getElementById('navSearchClear');
    const footer      = document.getElementById('searchFooter');
    const header      = document.getElementById('searchDropdownHeader');
    let searchTimer   = null;

    function formatPrice(p) {
        return new Intl.NumberFormat('vi-VN').format(p);
    }

    function highlightMatch(text, query) {
        if (!query) return text;
        const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return text.replace(new RegExp('(' + escaped + ')', 'gi'), '<mark>$1</mark>');
    }

    function renderResults(query) {
        const q = query.trim().toLowerCase();
        if (!q) { closeDropdown(); return; }

        // Lọc theo tên món ăn
        const matched = FOOD_DATA.filter(f => f.name.toLowerCase().includes(q));

        openDropdown();

        if (matched.length === 0) {
            header.textContent = 'Không tìm thấy kết quả';
            resultsList.innerHTML = `
                <div class="search-no-result">
                    <div class="search-no-result-icon">🔍</div>
                    <div>Không tìm thấy món "<strong>${query}</strong>"</div>
                    <div style="font-size:12px;margin-top:4px;color:var(--ink-lt)">Thử tìm từ khóa khác nhé!</div>
                </div>`;
            footer.style.display = 'none';
            return;
        }

        header.textContent = `Tìm thấy ${matched.length} món`;

        const MAX_SHOW = 8;
        const shown = matched.slice(0, MAX_SHOW);

        resultsList.innerHTML = shown.map(food => {
            const displayPrice = food.voucher_price !== null ? food.voucher_price : food.price;
            const hasDiscount  = food.voucher_price !== null;
            let badgeHtml = '';
            if (food.is_hot)       badgeHtml = '<span class="search-result-badge hot">🔥 HOT</span>';
            else if (food.is_sale) badgeHtml = '<span class="search-result-badge sale">🏷️ Sale</span>';
            else                   badgeHtml = '<span class="search-result-badge normal">Nổi bật</span>';

            const priceHtml = hasDiscount
                ? `<span class="original">${formatPrice(food.price)}đ</span>${formatPrice(displayPrice)}đ`
                : `${formatPrice(displayPrice)}đ`;

            return `
                <a class="search-result-item" href="#menu" onclick="scrollToFoodCard(${food.id}, event)">
                    <img class="search-result-img"
                         src="${food.image}"
                         onerror="this.src='https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=200&q=60'"
                         alt="${food.name}" loading="lazy">
                    <div class="search-result-info">
                        <div class="search-result-name">${highlightMatch(food.name, query)}</div>
                        <div class="search-result-price">${priceHtml}</div>
                    </div>
                    ${badgeHtml}
                </a>`;
        }).join('');

        if (matched.length > MAX_SHOW) {
            footer.style.display = 'block';
            footer.textContent = `Và ${matched.length - MAX_SHOW} món khác — cuộn xuống để xem thêm`;
        } else {
            footer.style.display = 'none';
        }
    }

    function openDropdown()  { dropdown.classList.add('open'); }
    function closeDropdown() { dropdown.classList.remove('open'); }

    // Cuộn đến food card và highlight
    window.scrollToFoodCard = function(foodId, e) {
        e.preventDefault();
        closeDropdown();
        input.value = '';
        clearBtn.classList.remove('visible');

        const forms = document.querySelectorAll(`form[action="/cart/add/${foodId}"]`);
        if (forms.length > 0) {
            const card = forms[0].closest('.food-card');
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.style.transition = 'box-shadow 0.3s, border-color 0.3s, transform 0.3s';
                card.style.boxShadow = '0 0 0 3px var(--brand), 0 20px 48px rgba(232,80,26,0.25)';
                card.style.borderColor = 'var(--brand)';
                setTimeout(() => {
                    card.style.boxShadow = '';
                    card.style.borderColor = '';
                }, 2200);
                return;
            }
        }
        document.getElementById('menu').scrollIntoView({ behavior: 'smooth' });
    };

    // Input handler với debounce
    input.addEventListener('input', () => {
        const val = input.value;
        clearBtn.classList.toggle('visible', val.length > 0);
        clearTimeout(searchTimer);
        if (val.trim() === '') { closeDropdown(); return; }
        searchTimer = setTimeout(() => renderResults(val), 180);
    });

    // Xóa nhanh
    clearBtn.addEventListener('click', () => {
        input.value = '';
        clearBtn.classList.remove('visible');
        closeDropdown();
        input.focus();
    });

    // Đóng khi click ngoài
    document.addEventListener('click', (e) => {
        if (!document.getElementById('navSearchWrap').contains(e.target)) {
            closeDropdown();
        }
    });

    // Keyboard: Escape đóng dropdown
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { closeDropdown(); input.blur(); }
    });

    // Mở lại khi focus và đã có query
    input.addEventListener('focus', () => {
        if (input.value.trim().length > 0) renderResults(input.value);
    });
})();

// ── Polling badge tin nhắn chưa đọc ──
@if(session('user'))
async function checkChatUnread() {
    try {
        const res  = await fetch('/chat/poll?last_id=0');
        const data = await res.json();
        if (data.messages) {
            const unread = data.messages.filter(m => m.sender === 'admin' && !m.is_read).length;
            const badge  = document.getElementById('chatBadge');
            if (badge) {
                badge.textContent = unread;
                badge.style.display = unread > 0 ? 'flex' : 'none';
            }
        }
    } catch(e) {}
}
checkChatUnread();
setInterval(checkChatUnread, 10000);
@endif
</script>
<script src="/chatbox.js"></script>
</body>
</html>