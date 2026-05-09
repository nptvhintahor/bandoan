<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShop — Đăng nhập</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
            background: #FFF8EE;
            overflow: hidden;
        }

        /* ===== BACKGROUND SVG ===== */
        .bg-illustration {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* ===== MAIN LAYOUT ===== */
        .page-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        /* ===== LOGO TOP-LEFT ===== */
        .logo-fixed {
            position: fixed;
            top: 24px;
            left: 28px;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.92);
            padding: 10px 18px 10px 14px;
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(232,80,26,0.10);
            backdrop-filter: blur(8px);
        }
        .logo-icon svg { display: block; }
        .logo-text {
            font-family: 'Fredoka One', sans-serif;
            font-size: 22px;
            color: #E8501A;
            letter-spacing: 0.3px;
            line-height: 1;
        }

        /* ===== AUTH CARD ===== */
        .auth-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(232,80,26,0.12);
            border-radius: 24px;
            padding: 2.5rem 2.2rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow:
                0 4px 32px rgba(232,80,26,0.08),
                0 1px 4px rgba(0,0,0,0.06);
            backdrop-filter: blur(12px);
        }

        .card-header {
            text-align: center;
            margin-bottom: 1.8rem;
        }
        .card-header h1 {
            font-family: 'Fredoka One', sans-serif;
            font-size: 26px;
            color: #E8501A;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }
        .card-header p {
            font-size: 13.5px;
            color: #A07850;
        }

        /* ===== TABS ===== */
        .tab-row {
            display: flex;
            gap: 4px;
            background: #FFF0E5;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 1.8rem;
        }
        .tab-btn {
            flex: 1;
            padding: 9px 0;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.22s ease;
            background: transparent;
            color: #B07040;
            font-family: 'Nunito', sans-serif;
        }
        .tab-btn.active {
            background: #fff;
            color: #E8501A;
            box-shadow: 0 1px 8px rgba(232,80,26,0.12);
            border: 1px solid rgba(232,80,26,0.15);
        }

        /* ===== ALERTS ===== */
        .alert {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 1.2rem;
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .alert-success {
            background: #F0FDF4;
            color: #166534;
            border: 1px solid #BBF7D0;
        }
        .alert-error {
            background: #FFF1EE;
            color: #B91C1C;
            border: 1px solid #FECACA;
        }
        .alert-icon { font-size: 15px; margin-top: 1px; flex-shrink: 0; }

        /* ===== FIELDS ===== */
        .field { margin-bottom: 1rem; }
        .field label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #A07850;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .field input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #F0DDD0;
            border-radius: 10px;
            font-size: 14.5px;
            font-family: 'Nunito', sans-serif;
            color: #3D2010;
            background: #FFFAF7;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        .field input::placeholder { color: #C0A090; }
        .field input:focus {
            border-color: #E8501A;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(232,80,26,0.10);
        }

        /* ===== SUBMIT BUTTON ===== */
        .submit-btn {
            width: 100%;
            padding: 12px 0;
            margin-top: 0.4rem;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            letter-spacing: 0.3px;
            transition: all 0.2s ease;
            background: linear-gradient(135deg, #E8501A 0%, #F07030 100%);
            color: #fff;
            box-shadow: 0 3px 14px rgba(232,80,26,0.28);
        }
        .submit-btn:hover {
            background: linear-gradient(135deg, #D04010 0%, #E8601A 100%);
            box-shadow: 0 5px 20px rgba(232,80,26,0.35);
            transform: translateY(-1px);
        }
        .submit-btn:active { transform: scale(0.99) translateY(0); }

        /* ===== FORM SECTIONS ===== */
        .form-section { display: none; }
        .form-section.visible {
            display: block;
            animation: fadeUp 0.22s ease;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ===== FOOTER ===== */
        .auth-footer {
            margin-top: 1.4rem;
            text-align: center;
            font-size: 12px;
            color: #C0A088;
            line-height: 1.6;
        }
        .auth-footer a { color: #E8501A; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }

        /* ===== DIVIDER ===== */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1.2rem 0;
            color: #D0B8A8;
            font-size: 12px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #F0DDD0;
        }
    </style>
</head>
<body>

<!-- ===== FOOD BACKGROUND ILLUSTRATION ===== -->
<svg class="bg-illustration" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
    <!-- Warm cream bg -->
    <rect width="1440" height="900" fill="#FFF8EE"/>
    <!-- Subtle diagonal lines -->
    <line x1="0" y1="0" x2="1440" y2="900" stroke="#F5E8D0" stroke-width="1"/>
    <line x1="1440" y1="0" x2="0" y2="900" stroke="#F5E8D0" stroke-width="1"/>
    <line x1="0" y1="450" x2="1440" y2="450" stroke="#F5E8D0" stroke-width="0.5"/>
    <line x1="720" y1="0" x2="720" y2="900" stroke="#F5E8D0" stroke-width="0.5"/>

    <!-- ===== TOP LEFT: Tomato ===== -->
    <circle cx="100" cy="100" r="58" fill="#E74C3C"/>
    <path d="M 82 50 Q 100 34 118 50" fill="none" stroke="#27AE60" stroke-width="5" stroke-linecap="round"/>
    <line x1="100" y1="36" x2="100" y2="48" stroke="#27AE60" stroke-width="5"/>
    <line x1="82" y1="50" x2="86" y2="60" stroke="#27AE60" stroke-width="3.5"/>
    <line x1="118" y1="50" x2="114" y2="60" stroke="#27AE60" stroke-width="3.5"/>
    <path d="M 68 100 Q 100 86 132 100" fill="none" stroke="#C0392B" stroke-width="2.5"/>
    <path d="M 70 112 Q 100 100 130 112" fill="none" stroke="#C0392B" stroke-width="1.5"/>

    <!-- ===== BOTTOM LEFT: Avocado ===== -->
    <ellipse cx="80" cy="780" rx="55" ry="76" fill="#27AE60"/>
    <ellipse cx="80" cy="785" rx="42" ry="60" fill="#A8E6A3"/>
    <ellipse cx="80" cy="795" rx="22" ry="30" fill="#5D4037"/>

    <!-- ===== BOTTOM LEFT 2: Lemon slice ===== -->
    <circle cx="240" cy="830" r="64" fill="#F4D03F"/>
    <circle cx="240" cy="830" r="54" fill="#F9E79F"/>
    <line x1="240" y1="776" x2="240" y2="884" stroke="#F4D03F" stroke-width="2.5"/>
    <line x1="186" y1="830" x2="294" y2="830" stroke="#F4D03F" stroke-width="2.5"/>
    <line x1="202" y1="792" x2="278" y2="868" stroke="#F4D03F" stroke-width="2.5"/>
    <line x1="202" y1="868" x2="278" y2="792" stroke="#F4D03F" stroke-width="2.5"/>
    <circle cx="240" cy="830" r="12" fill="#F4D03F"/>

    <!-- ===== TOP CENTER: Chili peppers ===== -->
    <path d="M 500 30 Q 540 10 560 55 Q 550 90 525 85 Q 498 78 500 30 Z" fill="#E74C3C"/>
    <line x1="500" y1="30" x2="492" y2="16" stroke="#27AE60" stroke-width="4" stroke-linecap="round"/>
    <path d="M 492 16 Q 480 8 482 0" fill="none" stroke="#27AE60" stroke-width="3.5" stroke-linecap="round"/>
    <path d="M 610 50 Q 640 25 660 65 Q 655 95 635 90 Q 610 82 610 50 Z" fill="#C0392B"/>
    <line x1="610" y1="50" x2="604" y2="38" stroke="#27AE60" stroke-width="4" stroke-linecap="round"/>

    <!-- ===== TOP RIGHT: Big Ramen Bowl ===== -->
    <ellipse cx="1280" cy="130" rx="130" ry="46" fill="#C0392B"/>
    <path d="M 1150 130 Q 1150 270 1280 290 Q 1410 270 1410 130 Z" fill="#E74C3C"/>
    <ellipse cx="1280" cy="130" rx="118" ry="38" fill="#F39C12"/>
    <!-- noodle waves -->
    <path d="M 1188 120 Q 1198 106 1208 120 Q 1218 134 1228 120 Q 1238 106 1248 120 Q 1258 134 1268 120 Q 1278 106 1288 120 Q 1298 134 1308 120 Q 1318 106 1328 120 Q 1338 134 1348 120 Q 1358 106 1368 120" fill="none" stroke="#F5CBA7" stroke-width="5" stroke-linecap="round"/>
    <path d="M 1178 140 Q 1190 126 1202 140 Q 1214 154 1226 140 Q 1238 126 1250 140 Q 1262 154 1274 140 Q 1286 126 1298 140 Q 1310 154 1322 140 Q 1334 126 1346 140 Q 1358 154 1370 140" fill="none" stroke="#F5CBA7" stroke-width="5" stroke-linecap="round"/>
    <!-- egg -->
    <ellipse cx="1330" cy="112" rx="28" ry="22" fill="#F5F5F5"/>
    <ellipse cx="1330" cy="114" rx="15" ry="13" fill="#F39C12"/>
    <!-- green onion -->
    <line x1="1245" y1="90" x2="1242" y2="165" stroke="#27AE60" stroke-width="4" stroke-linecap="round"/>
    <path d="M 1240 90 Q 1245 80 1250 90" fill="#27AE60"/>
    <!-- chopsticks -->
    <line x1="1260" y1="60" x2="1305" y2="155" stroke="#8B5E3C" stroke-width="6" stroke-linecap="round"/>
    <line x1="1278" y1="55" x2="1323" y2="150" stroke="#8B5E3C" stroke-width="6" stroke-linecap="round"/>

    <!-- ===== RIGHT MIDDLE: Burger ===== -->
    <ellipse cx="1360" cy="480" rx="88" ry="24" fill="#D4874B"/>
    <rect x="1272" y="432" width="176" height="36" rx="14" fill="#6B3A2A"/>
    <rect x="1264" y="416" width="192" height="24" rx="10" fill="#F5C842"/>
    <path d="M 1260 414 Q 1278 394 1296 414 Q 1314 394 1332 414 Q 1350 394 1368 414 Q 1386 394 1404 414 Q 1422 394 1440 414" fill="none" stroke="#27AE60" stroke-width="8" stroke-linecap="round"/>
    <path d="M 1278 408 Q 1360 348 1442 408 Z" fill="#E8A060"/>
    <ellipse cx="1360" cy="408" rx="82" ry="22" fill="#E8A060"/>
    <ellipse cx="1342" cy="386" rx="7" ry="5" fill="#D4874B" transform="rotate(-20,1342,386)"/>
    <ellipse cx="1362" cy="374" rx="7" ry="5" fill="#D4874B" transform="rotate(10,1362,374)"/>
    <ellipse cx="1382" cy="384" rx="7" ry="5" fill="#D4874B" transform="rotate(-15,1382,384)"/>
    <ellipse cx="1352" cy="378" rx="5" ry="4" fill="#D4874B"/>

    <!-- ===== LEFT MIDDLE: Pizza slice ===== -->
    <path d="M 60 460 Q 160 300 260 380 Q 280 480 210 540 Z" fill="#D4874B"/>
    <path d="M 60 460 Q 158 308 254 386 Q 268 462 214 516 Q 168 548 110 530 Z" fill="#F5C87A"/>
    <path d="M 72 464 Q 158 318 248 392 Q 260 460 210 510 Q 168 538 114 522 Z" fill="#D94F38"/>
    <circle cx="148" cy="392" r="22" fill="#F7D96A"/>
    <circle cx="190" cy="372" r="18" fill="#F7D96A"/>
    <circle cx="210" cy="415" r="20" fill="#F7D96A"/>
    <circle cx="165" cy="440" r="16" fill="#F7D96A"/>
    <circle cx="152" cy="386" r="15" fill="#A0291A"/>
    <circle cx="194" cy="364" r="13" fill="#A0291A"/>
    <circle cx="172" cy="430" r="13" fill="#A0291A"/>
    <circle cx="210" cy="408" r="12" fill="#A0291A"/>
    <path d="M 60 460 Q 160 300 260 380" fill="none" stroke="#E8A060" stroke-width="10" stroke-linecap="round"/>

    <!-- ===== BOTTOM RIGHT: Strawberry ===== -->
    <path d="M 1320 760 Q 1320 830 1370 860 Q 1420 830 1420 760 Q 1390 730 1370 750 Q 1350 730 1320 760 Z" fill="#E74C3C"/>
    <path d="M 1360 750 Q 1370 730 1380 750" fill="none" stroke="#27AE60" stroke-width="5" stroke-linecap="round"/>
    <line x1="1370" y1="732" x2="1370" y2="748" stroke="#27AE60" stroke-width="4"/>
    <!-- seeds -->
    <ellipse cx="1348" cy="778" rx="4" ry="5" fill="#FFF0EE"/>
    <ellipse cx="1368" cy="768" rx="4" ry="5" fill="#FFF0EE"/>
    <ellipse cx="1388" cy="778" rx="4" ry="5" fill="#FFF0EE"/>
    <ellipse cx="1355" cy="800" rx="4" ry="5" fill="#FFF0EE"/>
    <ellipse cx="1378" cy="800" rx="4" ry="5" fill="#FFF0EE"/>
    <ellipse cx="1365" cy="820" rx="4" ry="5" fill="#FFF0EE"/>

    <!-- ===== CENTER TOP: Garlic ===== -->
    <ellipse cx="800" cy="65" rx="40" ry="50" fill="#FAF0DC"/>
    <path d="M 776 58 Q 800 38 824 58 Q 824 90 800 100 Q 776 90 776 58 Z" fill="#F5E6C0"/>
    <line x1="800" y1="38" x2="800" y2="22" stroke="#9B8060" stroke-width="4"/>
    <path d="M 800 22 Q 808 14 806 6" fill="none" stroke="#9B8060" stroke-width="3" stroke-linecap="round"/>

    <!-- ===== HERBS bottom center ===== -->
    <ellipse cx="720" cy="845" rx="42" ry="28" fill="#1E8449" transform="rotate(-30,720,845)"/>
    <ellipse cx="768" cy="855" rx="36" ry="24" fill="#27AE60" transform="rotate(20,768,855)"/>
    <ellipse cx="742" cy="870" rx="33" ry="21" fill="#1E8449" transform="rotate(-10,742,870)"/>
    <line x1="720" y1="845" x2="768" y2="874" stroke="#145A32" stroke-width="3"/>
    <ellipse cx="670" cy="870" rx="34" ry="20" fill="#27AE60" transform="rotate(15,670,870)"/>

    <!-- ===== Mushrooms center-left ===== -->
    <path d="M 340 640 Q 340 690 362 700 Q 384 706 384 682 Q 384 640 384 640 Z" fill="#D5DBDB"/>
    <path d="M 330 640 Q 362 590 394 640 Z" fill="#A04000"/>
    <path d="M 330 640 Q 362 600 394 640" fill="none" stroke="#784212" stroke-width="2"/>

    <!-- ===== Pepper corns scattered ===== -->
    <circle cx="380" cy="200" r="7" fill="#2C3E50" opacity="0.7"/>
    <circle cx="398" cy="220" r="6" fill="#2C3E50" opacity="0.7"/>
    <circle cx="366" cy="228" r="7" fill="#2C3E50" opacity="0.7"/>
    <circle cx="390" cy="192" r="5" fill="#2C3E50" opacity="0.6"/>
    <circle cx="1050" cy="760" r="7" fill="#2C3E50" opacity="0.6"/>
    <circle cx="1068" cy="780" r="6" fill="#2C3E50" opacity="0.6"/>
    <circle cx="1040" cy="788" r="7" fill="#2C3E50" opacity="0.5"/>

    <!-- ===== Salt crystals ===== -->
    <rect x="960" y="80" width="12" height="12" rx="2" fill="#BDC3C7" opacity="0.7"/>
    <rect x="978" y="66" width="10" height="10" rx="2" fill="#BDC3C7" opacity="0.7"/>
    <rect x="994" y="82" width="12" height="12" rx="2" fill="#BDC3C7" opacity="0.7"/>
    <rect x="970" y="98" width="10" height="10" rx="2" fill="#BDC3C7" opacity="0.6"/>

    <!-- ===== Carrot center-right ===== -->
    <path d="M 1080 640 Q 1086 610 1096 600 Q 1106 598 1110 616 Q 1106 640 1088 652 Z" fill="#E67E22"/>
    <line x1="1096" y1="600" x2="1088" y2="576" stroke="#27AE60" stroke-width="4" stroke-linecap="round"/>
    <line x1="1096" y1="600" x2="1078" y2="580" stroke="#27AE60" stroke-width="4" stroke-linecap="round"/>
    <line x1="1096" y1="600" x2="1110" y2="582" stroke="#27AE60" stroke-width="4" stroke-linecap="round"/>

    <!-- ===== Olive oil drizzle ===== -->
    <path d="M 460 740 Q 470 760 466 790 Q 462 820 470 845" fill="none" stroke="#D4AC0D" stroke-width="6" stroke-linecap="round"/>
    <ellipse cx="460" cy="734" rx="14" ry="10" fill="#D4AC0D"/>

    <!-- ===== Stars / sparkles ===== -->
    <path d="M 700 200 L 706 186 L 712 200 L 726 200 L 716 208 L 720 222 L 706 214 L 692 222 L 696 208 L 686 200 Z" fill="#F4D03F" opacity="0.65"/>
    <path d="M 1100 300 L 1104 290 L 1108 300 L 1118 300 L 1110 306 L 1113 316 L 1104 310 L 1095 316 L 1098 306 L 1090 300 Z" fill="#F4D03F" opacity="0.5"/>
    <path d="M 200 500 L 203 492 L 206 500 L 214 500 L 208 505 L 210 513 L 203 508 L 196 513 L 198 505 L 192 500 Z" fill="#F4D03F" opacity="0.45"/>
    <path d="M 900 800 L 904 790 L 908 800 L 918 800 L 910 806 L 913 816 L 904 810 L 895 816 L 898 806 L 890 800 Z" fill="#F4D03F" opacity="0.5"/>

    <!-- ===== Subtle dot pattern ===== -->
    <circle cx="550" cy="680" r="4" fill="#F0C8A0" opacity="0.5"/>
    <circle cx="575" cy="700" r="3" fill="#F0C8A0" opacity="0.4"/>
    <circle cx="560" cy="715" r="4" fill="#F0C8A0" opacity="0.4"/>
    <circle cx="850" cy="200" r="4" fill="#F0C8A0" opacity="0.5"/>
    <circle cx="875" cy="185" r="3" fill="#F0C8A0" opacity="0.4"/>
    <circle cx="858" cy="215" r="4" fill="#F0C8A0" opacity="0.4"/>
</svg>

<!-- ===== FIXED LOGO TOP LEFT ===== -->
<div class="logo-fixed">
    <div class="logo-icon">
        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 6 16 Q 14 4 22 16 Z" fill="#E8A060"/>
            <ellipse cx="14" cy="16" rx="8" ry="3.5" fill="#E8A060"/>
            <rect x="6" y="18" width="16" height="4" rx="2.5" fill="#6B3A2A"/>
            <ellipse cx="14" cy="22" rx="8" ry="3" fill="#D4874B"/>
            <ellipse cx="10" cy="11" rx="2" ry="1.4" fill="#D4874B" transform="rotate(-20,10,11)"/>
            <ellipse cx="14" cy="9" rx="2" ry="1.4" fill="#D4874B" transform="rotate(5,14,9)"/>
            <ellipse cx="18" cy="11" rx="2" ry="1.4" fill="#D4874B" transform="rotate(20,18,11)"/>
        </svg>
    </div>
    <span class="logo-text">FoodShop</span>
</div>

<!-- ===== AUTH CARD ===== -->
<div class="page-wrapper">
    <div class="auth-card">

        <div class="card-header">
            <h1>Chào mừng! 🍔</h1>
            <p>Đặt món ngon — giao tận nơi, nhanh chóng</p>
        </div>

        <div class="tab-row">
            <button class="tab-btn active" id="tab-login" onclick="showLogin()">Đăng nhập</button>
            <button class="tab-btn" id="tab-register" onclick="showRegister()">Đăng ký</button>
        </div>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="alert alert-success">
                <span class="alert-icon">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- ERROR --}}
        @if(session('error'))
            <div class="alert alert-error">
                <span class="alert-icon">!</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- VALIDATE ERRORS --}}
        @if($errors->any())
            <div class="alert alert-error">
                <span class="alert-icon">!</span>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- LOGIN FORM -->
        <div id="loginSection" class="form-section visible">
            <form method="POST" action="/login">
                @csrf
                <div class="field">
                    <label>Email</label>
                    <input name="email" type="email" placeholder="you@example.com" required>
                </div>
                <div class="field">
                    <label>Mật khẩu</label>
                    <input name="password" type="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="submit-btn">Đăng nhập</button>
            </form>
            <div class="divider">hoặc</div>
            <div class="auth-footer">
                Chưa có tài khoản?
                <a href="#" onclick="showRegister(); return false;">Đăng ký ngay</a>
            </div>
        </div>

        <!-- REGISTER FORM -->
        <div id="registerSection" class="form-section">
            <form method="POST" action="/register">
                @csrf
                <div class="field">
                    <label>Họ tên</label>
                    <input name="name" placeholder="Nguyễn Văn A" required>
                </div>
                <div class="field">
                    <label>Email</label>
                    <input name="email" type="email" placeholder="you@example.com" required>
                </div>
                <div class="field">
                    <label>Mật khẩu</label>
                    <input name="password" type="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="submit-btn">Tạo tài khoản</button>
            </form>
            <div class="divider">hoặc</div>
            <div class="auth-footer">
                Đã có tài khoản?
                <a href="#" onclick="showLogin(); return false;">Đăng nhập</a>
            </div>
        </div>

        <div class="auth-footer" style="margin-top:1rem; border-top: 1px solid #F0DDD0; padding-top: 1rem;">
            Bằng cách tiếp tục, bạn đồng ý với
            <a href="#">Điều khoản dịch vụ</a> và
            <a href="#">Chính sách bảo mật</a>
        </div>
    </div>
</div>

<script>
function showLogin() {
    document.getElementById('loginSection').className  = 'form-section visible';
    document.getElementById('registerSection').className = 'form-section';
    document.getElementById('tab-login').className    = 'tab-btn active';
    document.getElementById('tab-register').className = 'tab-btn';
}
function showRegister() {
    document.getElementById('loginSection').className  = 'form-section';
    document.getElementById('registerSection').className = 'form-section visible';
    document.getElementById('tab-login').className    = 'tab-btn';
    document.getElementById('tab-register').className = 'tab-btn active';
}

// Auto-show register tab if there were register errors
@if(old('name') || (session('_old_input') && isset(session('_old_input')['name'])))
    showRegister();
@endif
</script>

</body>
</html>