<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử đơn hàng - FoodShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Be Vietnam Pro', sans-serif; }

        /* ── Status badge ── */
        .badge-processing { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
        .badge-shipping   { background:#dbeafe; color:#1e40af; border:1px solid #93c5fd; }
        .badge-done       { background:#dcfce7; color:#166534; border:1px solid #86efac; }
        .badge-cancelled  { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
        .badge-default    { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }

        /* ── Timeline ── */
        .timeline-step { position: relative; flex: 1; text-align: center; }
        .timeline-step::before {
            content: '';
            position: absolute; top: 16px; left: -50%; right: 50%;
            height: 2px; background: #e5e7eb; z-index: 0;
        }
        .timeline-step:first-child::before { display: none; }
        .timeline-step.done::before  { background: #ef4444; }
        .timeline-dot {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 8px; font-size: 14px; position: relative; z-index: 1;
            border: 2px solid #e5e7eb; background: #fff;
        }
        .timeline-step.done  .timeline-dot { border-color: #ef4444; background: #ef4444; color: #fff; }
        .timeline-step.active .timeline-dot { border-color: #ef4444; background: #fff7f7; color: #ef4444; }

        /* ── Order card ── */
        .order-card {
            background: #fff; border-radius: 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            overflow: hidden; transition: box-shadow 0.2s;
        }
        .order-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.09); }

        /* ── Filter tabs ── */
        .filter-tab {
            padding: 8px 18px; border-radius: 999px; font-size: 13px;
            font-weight: 600; cursor: pointer; border: 1.5px solid #e5e7eb;
            background: #fff; color: #6b7280; transition: all 0.2s;
        }
        .filter-tab.active, .filter-tab:hover {
            background: #ef4444; color: #fff; border-color: #ef4444;
        }

        /* ── Collapse ── */
        .items-body { display: none; }
        .items-body.open { display: block; }
        .toggle-btn { transition: transform 0.2s; }
        .toggle-btn.open { transform: rotate(180deg); }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { animation: fadeUp 0.4s ease forwards; }
        .delay-1 { animation-delay: 0.05s; opacity: 0; }
        .delay-2 { animation-delay: 0.10s; opacity: 0; }
        .delay-3 { animation-delay: 0.15s; opacity: 0; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- ══ NAVBAR ══ -->
<nav class="bg-white shadow-sm fixed w-full z-50 border-b border-gray-100">
    <div class="max-w-5xl mx-auto px-6 py-4 flex justify-between items-center">
        <a href="/" class="text-xl font-extrabold text-red-500">🍔 FoodShop</a>
        <div class="flex items-center gap-5 text-sm">
            <a href="/" class="text-gray-500 hover:text-red-500 transition">Trang chủ</a>
            <a href="/#menu" class="text-gray-500 hover:text-red-500 transition">Menu</a>
            @if(session('user'))
                <a href="/profile" class="font-semibold hover:text-red-500 transition">
                    👤 {{ session('user')['name'] }}
                </a>
                <a href="/logout" class="text-red-500 hover:underline">Đăng xuất</a>
            @else
                <a href="/auth" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition">Đăng nhập</a>
            @endif
        </div>
    </div>
</nav>

<div class="pt-24 pb-16 max-w-5xl mx-auto px-4">

    <!-- ══ HEADER ══ -->
    <div class="fade-up delay-1 mb-8">
        <a href="/" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-red-500 transition mb-4">
            ← Quay về trang chủ
        </a>
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-red-500 mb-1">Tài khoản</p>
                <h1 class="text-3xl font-extrabold text-gray-800">Lịch sử đơn hàng</h1>
            </div>
            <a href="/#menu"
               class="inline-block bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition">
                + Đặt món mới
            </a>
        </div>
    </div>

    <!-- ══ THÔNG BÁO ══ -->
    @if(session('success'))
    <div class="fade-up delay-1 bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 mb-5 flex items-center gap-2 text-sm font-medium">
        ✅ {{ session('success') }}
    </div>
    @endif

    <!-- ══ STATS ══ -->
    @php
        $total    = count($orders);
        $done     = $orders->where('status', 'Hoàn thành')->count();
        $shipping = $orders->where('status', 'Đang giao')->count();
        $spent    = $orders->where('status', 'Hoàn thành')->sum('total');
    @endphp
    <div class="fade-up delay-2 grid grid-cols-2 sm:grid-cols-4 gap-3 mb-7">
        <div class="bg-white rounded-2xl p-4 border border-gray-100 text-center">
            <p class="text-2xl font-extrabold text-gray-800">{{ $total }}</p>
            <p class="text-xs text-gray-400 mt-1">Tổng đơn</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 text-center">
            <p class="text-2xl font-extrabold text-green-500">{{ $done }}</p>
            <p class="text-xs text-gray-400 mt-1">Hoàn thành</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 text-center">
            <p class="text-2xl font-extrabold text-blue-500">{{ $shipping }}</p>
            <p class="text-xs text-gray-400 mt-1">Đang giao</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 text-center">
            <p class="text-2xl font-extrabold text-red-500">{{ number_format($spent) }}đ</p>
            <p class="text-xs text-gray-400 mt-1">Đã chi tiêu</p>
        </div>
    </div>

    <!-- ══ FILTER TABS ══ -->
    <div class="fade-up delay-2 flex flex-wrap gap-2 mb-6">
        <button class="filter-tab active" onclick="filterOrders('all', this)">Tất cả ({{ $total }})</button>
        <button class="filter-tab" onclick="filterOrders('Đang xử lý', this)">⏳ Đang xử lý</button>
        <button class="filter-tab" onclick="filterOrders('Đang giao', this)">🚀 Đang giao</button>
        <button class="filter-tab" onclick="filterOrders('Hoàn thành', this)">✅ Hoàn thành</button>
        <button class="filter-tab" onclick="filterOrders('Đã hủy', this)">❌ Đã hủy</button>
    </div>

    <!-- ══ DANH SÁCH ĐƠN ══ -->
    @forelse($orders as $index => $order)
    @php
        $items = is_string($order->items) ? json_decode($order->items, true) ?? [] : ($order->items ?? []);

        $badgeClass = match($order->status) {
            'Đang xử lý' => 'badge-processing',
            'Đang giao'  => 'badge-shipping',
            'Hoàn thành' => 'badge-done',
            'Đã hủy'     => 'badge-cancelled',
            default      => 'badge-default',
        };

        $steps = [
            ['label' => 'Đặt hàng',   'icon' => '📋', 'statuses' => ['Đang xử lý','Đang giao','Hoàn thành']],
            ['label' => 'Xác nhận',   'icon' => '✅', 'statuses' => ['Đang giao','Hoàn thành']],
            ['label' => 'Đang giao',  'icon' => '🛵', 'statuses' => ['Đang giao','Hoàn thành']],
            ['label' => 'Hoàn thành', 'icon' => '🎉', 'statuses' => ['Hoàn thành']],
        ];
        $activeStep = match($order->status) {
            'Đang xử lý' => 0,
            'Đang giao'  => 2,
            'Hoàn thành' => 3,
            default      => -1,
        };
    @endphp

    <div class="order-card mb-4 fade-up" style="animation-delay:{{ $index * 0.06 }}s; opacity:0"
         data-status="{{ $order->status }}">

        <!-- Card header -->
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500 font-extrabold text-sm">
                    #{{ $order->id }}
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">Đơn hàng #{{ $order->id }}</p>
                    <p class="text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($order->created_at)->format('H:i — d/m/Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                    {{ $order->status }}
                </span>
                <span class="text-red-500 font-extrabold text-base">
                    {{ number_format($order->total) }}đ
                </span>
            </div>
        </div>

        <!-- Timeline -->
        @if($order->status !== 'Đã hủy')
        <div class="px-6 py-5 bg-gray-50/60 border-b border-gray-100">
            <div class="flex justify-between">
                @foreach($steps as $i => $step)
                @php
                    $isDone   = in_array($order->status, $step['statuses']);
                    $isActive = $i === $activeStep;
                    $cls = $isDone ? 'done' : ($isActive ? 'active' : '');
                @endphp
                <div class="timeline-step {{ $cls }}">
                    <div class="timeline-dot">{{ $step['icon'] }}</div>
                    <p class="text-xs font-semibold {{ $isDone ? 'text-red-500' : 'text-gray-400' }}">
                        {{ $step['label'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="px-6 py-3 bg-red-50 border-b border-red-100">
            <p class="text-sm text-red-500 font-medium text-center">❌ Đơn hàng này đã bị hủy</p>
        </div>
        @endif

        <!-- Meta row -->
        <div class="px-6 py-3 flex flex-wrap gap-4 text-xs text-gray-500 border-b border-gray-50">
            <span>
                @if($order->payment_method == 'cod')
                    💵 Thanh toán khi nhận hàng
                @else
                    💳 Thanh toán online
                @endif
            </span>
            <span>🛍️ {{ count($items) }} món</span>
        </div>

        <!-- Items (collapsible) -->
        <div class="px-6 py-3">
            <button onclick="toggleItems(this)" class="flex items-center justify-between w-full text-sm font-semibold text-gray-700 hover:text-red-500 transition">
                <span>Chi tiết đơn hàng</span>
                <svg class="toggle-btn w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div class="items-body mt-3">
                <div class="rounded-xl overflow-hidden border border-gray-100">
                    @foreach($items as $item)
                    <div class="flex justify-between items-center px-4 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-base">🍽️</div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-400">x{{ $item['quantity'] }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-700">
                            {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1)) }}đ
                        </span>
                    </div>
                    @endforeach

                    <!-- Tổng -->
                    <div class="flex justify-between items-center px-4 py-3 bg-red-50">
                        <span class="text-sm font-bold text-gray-700">Tổng cộng</span>
                        <span class="text-base font-extrabold text-red-500">{{ number_format($order->total) }}đ</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 mt-3 flex-wrap">
                    @if($order->status === 'Hoàn thành')
                    <a href="/#menu"
                       class="text-xs bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition font-semibold">
                        🔁 Đặt lại
                    </a>
                    @endif
                    @if($order->status === 'Đang xử lý')
                    <form action="/orders/{{ $order->id }}/cancel" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Bạn chắc chắn muốn hủy đơn này?')"
                            class="text-xs border border-red-200 text-red-500 px-4 py-2 rounded-lg hover:bg-red-50 transition font-semibold">
                            ❌ Hủy đơn
                        </button>
                    </form>
                    @endif
                    <button onclick="window.print()"
                        class="text-xs border border-gray-200 text-gray-500 px-4 py-2 rounded-lg hover:bg-gray-50 transition font-semibold">
                        🖨️ In hóa đơn
                    </button>
                </div>
            </div>
        </div>

    </div>
    @empty

    <!-- Empty state -->
    <div class="fade-up text-center py-24">
        <p class="text-6xl mb-4">📦</p>
        <h2 class="text-xl font-bold text-gray-700 mb-2">Chưa có đơn hàng nào</h2>
        <p class="text-gray-400 text-sm mb-6">Hãy đặt món yêu thích ngay hôm nay!</p>
        <a href="/#menu"
           class="inline-block bg-red-500 text-white px-8 py-3 rounded-xl font-bold hover:bg-red-600 transition">
            Khám phá menu
        </a>
    </div>

    @endforelse
</div>

<!-- FOOTER -->
<footer class="bg-gray-900 text-white py-8 text-center text-sm">
    <p>© 2026 FoodShop - Laravel Project</p>
</footer>

<script>
function toggleItems(btn) {
    const body = btn.closest('.px-6').querySelector('.items-body');
    const icon = btn.querySelector('.toggle-btn');
    body.classList.toggle('open');
    icon.classList.toggle('open');
}

function filterOrders(status, btn) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.order-card').forEach(card => {
        card.style.display =
            (status === 'all' || card.dataset.status === status) ? '' : 'none';
    });
}
</script>
</body>
</html>