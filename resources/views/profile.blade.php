<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ - FoodShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Be Vietnam Pro', sans-serif; }

        .tab-btn { transition: all 0.2s; }
        .tab-btn.active {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .badge-gold   { background:#fef3c7; color:#92400e; border:1px solid #fcd34d; }
        .badge-silver { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }
        .badge-bronze { background:#fdf2f8; color:#9d174d; border:1px solid #f9a8d4; }

        .status-pending   { background:#fef9c3; color:#854d0e; }
        .status-confirmed { background:#dbeafe; color:#1e40af; }
        .status-shipping  { background:#ede9fe; color:#5b21b6; }
        .status-done      { background:#dcfce7; color:#166534; }
        .status-cancelled { background:#fee2e2; color:#991b1b; }

        .point-bar-inner { transition: width 0.8s ease; }

        .card-hover { transition: box-shadow 0.2s, transform 0.2s; }
        .card-hover:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.08); transform: translateY(-1px); }

        /* Avatar upload styles */
        .avatar-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .avatar-overlay {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: rgba(0,0,0,0.45);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            gap: 3px;
        }
        .avatar-wrapper:hover .avatar-overlay { opacity: 1; }
        .avatar-wrapper:hover .avatar-img,
        .avatar-wrapper:hover .avatar-initials { filter: brightness(0.75); }
        .avatar-img { transition: filter 0.2s; }
        .avatar-initials { transition: filter 0.2s; }

        /* Modal crop */
        #avatar-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0,0,0,0.55);
            align-items: center;
            justify-content: center;
        }
        #avatar-modal-backdrop.flex { display: flex; }

        #crop-canvas {
            border-radius: 9999px;
            overflow: hidden;
            border: 3px solid #ef4444;
        }

        /* Preview ring animation */
        @keyframes spin-ring {
            to { transform: rotate(360deg); }
        }
        .uploading-ring {
            animation: spin-ring 1s linear infinite;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- NAVBAR -->
<nav class="bg-white shadow-md fixed w-full z-50">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="/" class="text-2xl font-bold text-red-500">🍔 FoodShop</a>
        <div class="space-x-6 flex items-center">
            <a href="/" class="hover:text-red-500">Trang chủ</a>
            <a href="#menu" class="hover:text-red-500">Menu</a>
            <a href="/cart" class="hover:text-red-500 relative">
                🛒
                <span class="bg-red-500 text-white px-2 rounded-full text-sm absolute -top-2 -right-3">
                    {{ count(session('cart', [])) }}
                </span>
            </a>
            @if(session('user'))
                <a href="/profile" class="font-semibold text-red-500 border-b-2 border-red-500 pb-0.5">
                    👤 {{ session('user')['name'] }}
                </a>
                <a href="/logout" class="text-red-500 hover:underline">Đăng xuất</a>
            @else
                <a href="/auth" class="hover:text-red-500">Tài khoản</a>
            @endif
        </div>
    </div>
</nav>

<div class="pt-24 pb-12">
<div class="container mx-auto px-4 max-w-5xl">

    <!-- HEADER PROFILE -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 flex flex-col md:flex-row items-center md:items-start gap-6">

        <!-- Avatar với tính năng upload -->
        <div class="relative flex-shrink-0">
            <div class="avatar-wrapper w-24 h-24" onclick="openAvatarModal()" title="Nhấn để đổi ảnh đại diện">

                @if(!empty($user->avatar))
                    {{-- Đã có ảnh: hiển thị ảnh thật --}}
                    <img id="avatar-display"
                         src="{{ asset('storage/' . $user->avatar) }}"
                         alt="Avatar"
                         class="avatar-img w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                @else
                    {{-- Chưa có ảnh: hiển thị initials --}}
                    <div id="avatar-display"
                         class="avatar-initials w-24 h-24 rounded-full bg-red-100 flex items-center justify-center text-4xl font-bold text-red-500 border-4 border-white shadow-md">
                        {{ strtoupper(substr(session('user')['name'] ?? 'U', 0, 1)) }}
                    </div>
                @endif

                <!-- Overlay hover -->
                <div class="avatar-overlay">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Đổi ảnh</span>
                </div>
            </div>

            <!-- Badge online -->
            <span class="absolute bottom-0 right-0 bg-green-400 border-2 border-white rounded-full w-5 h-5"></span>
        </div>

        <!-- Thông tin nhanh -->
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-2xl font-bold text-gray-800">{{ session('user')['name'] ?? 'Người dùng' }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ session('user')['email'] ?? '' }}</p>

            <!-- Hạng thành viên -->
            @php
                $points = $user->points ?? 0;
                if ($points >= 5000)      { $rank = 'Vàng';   $rankClass = 'badge-gold';   $icon = '👑'; }
                elseif ($points >= 2000)  { $rank = 'Bạc';    $rankClass = 'badge-silver'; $icon = '⭐'; }
                else                      { $rank = 'Đồng';   $rankClass = 'badge-bronze'; $icon = '🎖️'; }
            @endphp
            <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold {{ $rankClass }}">
                {{ $icon }} Thành viên {{ $rank }}
            </span>

            <!-- Thống kê nhanh -->
            <div class="grid grid-cols-3 gap-3 mt-4">
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-xl font-bold text-red-500">{{ $totalOrders ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Đơn hàng</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-xl font-bold text-red-500">{{ number_format($points) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Điểm tích lũy</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-xl font-bold text-red-500">{{ number_format($totalSpent ?? 0) }}đ</p>
                    <p class="text-xs text-gray-500 mt-1">Tổng chi tiêu</p>
                </div>
            </div>
        </div>

        <!-- Nút chỉnh sửa -->
        <div class="flex-shrink-0">
            <button onclick="openEditModal()"
                class="bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition text-sm font-medium">
                ✏️ Chỉnh sửa
            </button>
        </div>
    </div>

    <!-- TABS -->
    <div class="flex gap-2 mb-5 flex-wrap">
        <button class="tab-btn px-5 py-2 rounded-lg border border-gray-200 text-sm font-medium" onclick="switchTab('info', this)">
            👤 Thông tin
        </button>
        <button class="tab-btn px-5 py-2 rounded-lg border border-gray-200 text-sm font-medium" onclick="switchTab('orders', this)">
            📦 Đơn hàng
        </button>
        <button class="tab-btn px-5 py-2 rounded-lg border border-gray-200 text-sm font-medium" onclick="switchTab('points', this)">
            🎁 Tích điểm
        </button>
        <button class="tab-btn px-5 py-2 rounded-lg border border-gray-200 text-sm font-medium" onclick="switchTab('address', this)">
            📍 Địa chỉ
        </button>
        <button class="tab-btn px-5 py-2 rounded-lg border border-gray-200 text-sm font-medium" onclick="switchTab('security', this)">
            🔒 Bảo mật
        </button>
    </div>

    <!-- ============ TAB: THÔNG TIN CÁ NHÂN ============ -->
    <div id="tab-info" class="tab-content">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-5">Thông tin cá nhân</h2>

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-4 text-sm">
                ✅ {{ session('success') }}
            </div>
            @endif

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Họ và tên</label>
                    <p class="mt-1 text-gray-800 font-medium">{{ session('user')['name'] ?? '—' }}</p>
                    <div class="border-b border-gray-100 mt-2"></div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</label>
                    <p class="mt-1 text-gray-800 font-medium">{{ session('user')['email'] ?? '—' }}</p>
                    <div class="border-b border-gray-100 mt-2"></div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Số điện thoại</label>
                    <p class="mt-1 text-gray-800 font-medium">{{ $user->phone ?? 'Chưa cập nhật' }}</p>
                    <div class="border-b border-gray-100 mt-2"></div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ngày sinh</label>
                    <p class="mt-1 text-gray-800 font-medium">
                        {{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('d/m/Y') : 'Chưa cập nhật' }}
                    </p>
                    <div class="border-b border-gray-100 mt-2"></div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Giới tính</label>
                    <p class="mt-1 text-gray-800 font-medium">{{ $user->gender ?? 'Chưa cập nhật' }}</p>
                    <div class="border-b border-gray-100 mt-2"></div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ngày tham gia</label>
                    <p class="mt-1 text-gray-800 font-medium">
                        {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}
                    </p>
                    <div class="border-b border-gray-100 mt-2"></div>
                </div>
            </div>

            <button onclick="openEditModal()"
                class="mt-6 bg-red-500 text-white px-6 py-2.5 rounded-lg hover:bg-red-600 transition text-sm font-medium">
                ✏️ Cập nhật thông tin
            </button>
        </div>
    </div>

    <!-- ============ TAB: ĐƠN HÀNG ============ -->
    <div id="tab-orders" class="tab-content">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-gray-800">Lịch sử đơn hàng</h2>
                <select class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-red-300"
                    onchange="filterOrders(this.value)">
                    <option value="all">Tất cả</option>
                    <option value="Chờ xác nhận">Chờ xác nhận</option>
                    <option value="Đã xác nhận">Đã xác nhận</option>
                    <option value="Đang giao">Đang giao</option>
                    <option value="Hoàn thành">Hoàn thành</option>
                    <option value="Đã hủy">Đã hủy</option>
                </select>
            </div>

            @forelse($orders ?? [] as $order)
            <div class="order-item border border-gray-100 rounded-xl p-4 mb-3 card-hover" data-status="{{ $order->status }}">
                <div class="flex justify-between items-start flex-wrap gap-2">
                    <div>
                        <p class="font-bold text-gray-800">#{{ $order->id }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ \Carbon\Carbon::parse($order->created_at)->format('H:i - d/m/Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        @php
                            $statusMap = [
                                'pending'      => ['label' => 'Chờ xác nhận', 'class' => 'status-pending'],
                                'confirmed'    => ['label' => 'Đã xác nhận',  'class' => 'status-confirmed'],
                                'shipping'     => ['label' => 'Đang giao',    'class' => 'status-shipping'],
                                'done'         => ['label' => 'Hoàn thành',   'class' => 'status-done'],
                                'cancelled'    => ['label' => 'Đã hủy',       'class' => 'status-cancelled'],
                                'Chờ xác nhận' => ['label' => 'Chờ xác nhận', 'class' => 'status-pending'],
                                'Đã xác nhận'  => ['label' => 'Đã xác nhận',  'class' => 'status-confirmed'],
                                'Đang giao'    => ['label' => 'Đang giao',    'class' => 'status-shipping'],
                                'Hoàn thành'   => ['label' => 'Hoàn thành',   'class' => 'status-done'],
                                'Đã hủy'       => ['label' => 'Đã hủy',       'class' => 'status-cancelled'],
                            ];
                            $s = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'status-pending'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $s['class'] }}">
                            {{ $s['label'] }}
                        </span>
                        <p class="font-bold text-red-500 text-right mt-1">
                            {{ number_format($order->total ?? $order->total_price ?? 0) }}đ
                        </p>
                    </div>
                </div>

                <!-- Danh sách món -->
                <div class="mt-3 pt-3 border-t border-gray-50 space-y-1">
                    @foreach($order->items ?? [] as $item)
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>{{ $item['name'] ?? 'Món ăn' }} x{{ $item['quantity'] }}</span>
                        <span>{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1)) }}đ</span>
                    </div>
                    @endforeach
                </div>

                <!-- Hành động -->
                <div class="flex gap-2 mt-3 flex-wrap">
                    <a href="/orders/{{ $order->id }}"
                       class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition font-medium">
                        Xem chi tiết
                    </a>
                    @if(in_array($order->status, ['pending', 'Chờ xác nhận']))
                    <form action="/orders/{{ $order->id }}/cancel" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-xs border border-red-200 text-red-500 rounded-lg px-3 py-1.5 hover:bg-red-50 transition font-medium">
                            Hủy đơn
                        </button>
                    </form>
                    @endif
                    @if(in_array($order->status, ['done', 'Hoàn thành']))
                    <a href="/orders/{{ $order->id }}/reorder"
                       class="text-xs bg-red-500 text-white rounded-lg px-3 py-1.5 hover:bg-red-600 transition font-medium">
                        Đặt lại
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-16 text-gray-400">
                <p class="text-5xl mb-3">📦</p>
                <p class="font-medium">Bạn chưa có đơn hàng nào</p>
                <a href="/#menu" class="mt-3 inline-block bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition text-sm">
                    Đặt món ngay
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- ============ TAB: TÍCH ĐIỂM ============ -->
    <div id="tab-points" class="tab-content">
        <div class="space-y-5">

            <!-- Điểm hiện tại & hạng -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Điểm tích lũy của bạn</h2>

                @php
                    $points   = $user->points ?? 0;
                    $nextRank = $points < 2000 ? 2000 : ($points < 5000 ? 5000 : 5000);
                    $progress = $points < 2000
                        ? min(100, round($points / 2000 * 100))
                        : ($points < 5000 ? min(100, round(($points - 2000) / 3000 * 100)) : 100);
                    $nextLabel = $points < 2000 ? 'Bạc' : ($points < 5000 ? 'Vàng' : 'Vàng');
                @endphp

                <div class="flex items-end gap-2 mb-2">
                    <span class="text-5xl font-bold text-red-500">{{ number_format($points) }}</span>
                    <span class="text-gray-400 mb-1 text-sm">điểm</span>
                </div>

                @if($points < 5000)
                <p class="text-sm text-gray-500 mb-2">
                    Cần thêm <span class="font-semibold text-gray-700">{{ number_format($nextRank - $points) }} điểm</span>
                    để lên hạng <span class="font-semibold text-yellow-600">{{ $nextLabel }}</span>
                </p>
                <div class="bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="point-bar-inner h-full bg-gradient-to-r from-red-400 to-red-500 rounded-full"
                         style="width: {{ $progress . '%' }}"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span>{{ number_format($points) }}</span>
                    <span>{{ number_format($nextRank) }}</span>
                </div>
                @else
                <p class="text-sm text-yellow-600 font-medium mt-1">👑 Bạn đã đạt hạng cao nhất!</p>
                @endif

                <!-- Quyền lợi theo hạng -->
                <div class="grid grid-cols-3 gap-3 mt-6">
                    <div class="text-center p-3 rounded-xl {{ $points < 2000 ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50' }}">
                        <p class="text-xl">🎖️</p>
                        <p class="text-xs font-bold mt-1">Đồng</p>
                        <p class="text-xs text-gray-500">0 - 1.999đ</p>
                        <p class="text-xs text-green-600 mt-1">Giảm 2%</p>
                    </div>
                    <div class="text-center p-3 rounded-xl {{ $points >= 2000 && $points < 5000 ? 'bg-slate-100 border border-slate-300' : 'bg-gray-50' }}">
                        <p class="text-xl">⭐</p>
                        <p class="text-xs font-bold mt-1">Bạc</p>
                        <p class="text-xs text-gray-500">2.000 - 4.999đ</p>
                        <p class="text-xs text-green-600 mt-1">Giảm 5%</p>
                    </div>
                    <div class="text-center p-3 rounded-xl {{ $points >= 5000 ? 'bg-yellow-50 border border-yellow-300' : 'bg-gray-50' }}">
                        <p class="text-xl">👑</p>
                        <p class="text-xs font-bold mt-1">Vàng</p>
                        <p class="text-xs text-gray-500">5.000+ điểm</p>
                        <p class="text-xs text-green-600 mt-1">Giảm 10%</p>
                    </div>
                </div>
            </div>

            <!-- Lịch sử điểm -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Lịch sử điểm</h2>
                @forelse($pointHistory ?? [] as $history)
                <div class="flex justify-between items-center py-3 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $history->description }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($history->created_at)->format('H:i - d/m/Y') }}</p>
                    </div>
                    <span class="{{ $history->amount > 0 ? 'text-green-500' : 'text-red-400' }} font-bold text-sm">
                        {{ $history->amount > 0 ? '+' : '' }}{{ number_format($history->amount) }} điểm
                    </span>
                </div>
                @empty
                <p class="text-center text-gray-400 py-8">Chưa có lịch sử điểm</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ============ TAB: ĐỊA CHỈ ============ -->
    <div id="tab-address" class="tab-content">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-lg font-bold text-gray-800">Địa chỉ giao hàng</h2>
                <button onclick="openAddressModal()"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600 transition font-medium">
                    + Thêm địa chỉ
                </button>
            </div>

            @if(session('address_success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-4 text-sm">
                ✅ {{ session('address_success') }}
            </div>
            @endif

            @forelse($addresses ?? [] as $address)
            <div class="border border-gray-100 rounded-xl p-4 mb-3 card-hover relative">
                @if($address->is_default)
                <span class="absolute top-3 right-3 bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                    Mặc định
                </span>
                @endif
                <p class="font-semibold text-gray-800">{{ $address->receiver_name }}</p>
                <p class="text-sm text-gray-500 mt-0.5">📞 {{ $address->phone }}</p>
                <p class="text-sm text-gray-600 mt-1">📍 {{ $address->address }}</p>
                <div class="flex gap-2 mt-3">
                    <button
                        onclick="openEditAddressModal({{ $address->id }}, '{{ addslashes($address->receiver_name) }}', '{{ addslashes($address->phone) }}', '{{ addslashes($address->address) }}', {{ $address->is_default ? 'true' : 'false' }})"
                        class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition font-medium">
                        Sửa
                    </button>
                    @if(!$address->is_default)
                    <form action="/address/{{ $address->id }}/default" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition font-medium">
                            Đặt mặc định
                        </button>
                    </form>
                    <form action="/address/{{ $address->id }}/delete" method="POST" class="inline"
                          onsubmit="return confirm('Bạn chắc chắn muốn xóa địa chỉ này?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="text-xs border border-red-200 text-red-500 rounded-lg px-3 py-1.5 hover:bg-red-50 transition font-medium">
                            Xóa
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
                <p class="text-4xl mb-3">📍</p>
                <p>Chưa có địa chỉ nào</p>
                <button onclick="openAddressModal()"
                    class="mt-3 inline-block bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition text-sm">
                    Thêm địa chỉ đầu tiên
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- ============ TAB: BẢO MẬT ============ -->
    <div id="tab-security" class="tab-content">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-5">Bảo mật tài khoản</h2>

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-4 text-sm">
                ✅ {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 mb-4 text-sm">
                ❌ {{ $errors->first() }}
            </div>
            @endif

            <form action="/profile/password" method="POST" class="max-w-md">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                        <input type="password" name="new_password" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                        <input type="password" name="new_password_confirmation" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                    </div>
                    <button type="submit"
                        class="bg-red-500 text-white px-6 py-2.5 rounded-lg hover:bg-red-600 transition text-sm font-medium">
                        Đổi mật khẩu
                    </button>
                </div>
            </form>

            <hr class="my-6 border-gray-100">

            <h3 class="font-semibold text-gray-700 mb-3">Hoạt động tài khoản</h3>
            <div class="flex items-center justify-between bg-gray-50 rounded-xl p-4">
                <div>
                    <p class="text-sm font-medium text-gray-800">Phiên hiện tại</p>
                    <p class="text-xs text-gray-400 mt-0.5">Trình duyệt của bạn — {{ now()->format('d/m/Y H:i') }}</p>
                </div>
                <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Đang hoạt động</span>
            </div>

            <hr class="my-6 border-gray-100">

            <h3 class="font-semibold text-red-600 mb-2">Vùng nguy hiểm</h3>
            <p class="text-sm text-gray-500 mb-3">Xóa tài khoản sẽ không thể khôi phục dữ liệu.</p>
            <button onclick="confirm('Bạn chắc chắn muốn xóa tài khoản?') && document.getElementById('delete-form').submit()"
                class="text-sm border border-red-300 text-red-500 px-5 py-2 rounded-lg hover:bg-red-50 transition">
                Xóa tài khoản
            </button>
            <form id="delete-form" action="/profile/delete" method="POST" class="hidden">
                @csrf @method('DELETE')
            </form>
        </div>
    </div>

</div>
</div>

<!-- ============ MODAL: ĐỔI ẢNH ĐẠI DIỆN ============ -->
<div id="avatar-modal-backdrop" onclick="handleBackdropClick(event)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6" onclick="event.stopPropagation()">

        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-800">Đổi ảnh đại diện</h3>
            <button onclick="closeAvatarModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
        </div>

        <!-- Preview vùng tròn -->
        <div class="flex flex-col items-center gap-4">

            <!-- Vòng preview -->
            <div class="relative">
                <div id="avatar-preview-ring"
                     class="w-36 h-36 rounded-full border-4 border-red-200 overflow-hidden bg-red-50 flex items-center justify-center shadow-md">
                    @if(!empty($user->avatar))
                        <img id="avatar-preview-img"
                             src="{{ asset('storage/' . $user->avatar) }}"
                             alt="Preview"
                             class="w-full h-full object-cover">
                    @else
                        <span id="avatar-preview-initials"
                              class="text-5xl font-bold text-red-400">
                            {{ strtoupper(substr(session('user')['name'] ?? 'U', 0, 1)) }}
                        </span>
                    @endif
                </div>
                <!-- Spinner khi đang upload -->
                <div id="avatar-spinner" class="hidden absolute inset-0 rounded-full bg-black bg-opacity-30 flex items-center justify-center">
                    <svg class="uploading-ring w-8 h-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                </div>
            </div>

            <!-- Thông tin hướng dẫn -->
            <p class="text-xs text-gray-400 text-center">Ảnh JPG, PNG hoặc GIF. Tối đa 2MB.</p>

            <!-- Input file (ẩn) -->
            <input type="file" id="avatar-file-input" accept="image/*" class="hidden" onchange="handleFileSelect(event)">

            <!-- Nút chọn ảnh -->
            <button onclick="document.getElementById('avatar-file-input').click()"
                class="w-full border-2 border-dashed border-red-300 rounded-xl py-3 text-sm text-red-500 font-medium hover:bg-red-50 transition flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Chọn ảnh từ thiết bị
            </button>

            <!-- Thông báo lỗi -->
            <p id="avatar-error" class="hidden text-xs text-red-500 text-center"></p>

            <!-- Form upload (ẩn, submit bằng JS) -->
            <form id="avatar-upload-form" action="/profile/avatar" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                <input type="file" name="avatar" id="avatar-form-file">
            </form>

            <!-- Nút hành động -->
            <div id="avatar-actions" class="hidden w-full flex gap-3">
                <button onclick="submitAvatarUpload()"
                    class="flex-1 bg-red-500 text-white py-2.5 rounded-lg hover:bg-red-600 transition text-sm font-medium">
                    💾 Lưu ảnh
                </button>
                <button onclick="cancelAvatarSelect()"
                    class="flex-1 border border-gray-200 py-2.5 rounded-lg hover:bg-gray-50 transition text-sm">
                    Hủy
                </button>
            </div>

            @if(!empty($user->avatar))
            <button onclick="removeAvatar()"
                class="text-xs text-gray-400 hover:text-red-400 transition underline underline-offset-2">
                Xóa ảnh đại diện
            </button>
            @endif

        </div>
    </div>
</div>

<!-- ============ MODAL: CHỈNH SỬA THÔNG TIN ============ -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-800">Chỉnh sửa thông tin</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>
        <form action="/profile/update" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                    <input type="text" name="name" value="{{ session('user')['name'] ?? '' }}"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="tel" name="phone" value="{{ $user->phone ?? '' }}"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                    <input type="date" name="birthday" value="{{ $user->birthday ?? '' }}"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                    <select name="gender"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                        <option value="">-- Chọn --</option>
                        <option value="Nam"  {{ ($user->gender ?? '') === 'Nam'  ? 'selected' : '' }}>Nam</option>
                        <option value="Nữ"   {{ ($user->gender ?? '') === 'Nữ'   ? 'selected' : '' }}>Nữ</option>
                        <option value="Khác" {{ ($user->gender ?? '') === 'Khác' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="flex-1 bg-red-500 text-white py-2.5 rounded-lg hover:bg-red-600 transition text-sm font-medium">
                    Lưu thay đổi
                </button>
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 border border-gray-200 py-2.5 rounded-lg hover:bg-gray-50 transition text-sm">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============ MODAL: THÊM ĐỊA CHỈ MỚI ============ -->
<div id="address-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-800">Thêm địa chỉ mới</h3>
            <button onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>
        <form action="/address/store" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên người nhận <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="receiver_name" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="Ví dụ: Nguyễn Văn A">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Số điện thoại <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="Ví dụ: 0912 345 678">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Địa chỉ <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" required rows="2"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"
                        placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" id="addr_is_default" value="1"
                        class="w-4 h-4 accent-red-500 cursor-pointer">
                    <label for="addr_is_default" class="text-sm text-gray-600 cursor-pointer">
                        Đặt làm địa chỉ mặc định
                    </label>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="flex-1 bg-red-500 text-white py-2.5 rounded-lg hover:bg-red-600 transition text-sm font-medium">
                    💾 Lưu địa chỉ
                </button>
                <button type="button" onclick="closeAddressModal()"
                    class="flex-1 border border-gray-200 py-2.5 rounded-lg hover:bg-gray-50 transition text-sm">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============ MODAL: SỬA ĐỊA CHỈ ============ -->
<div id="edit-address-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-800">Sửa địa chỉ</h3>
            <button onclick="closeEditAddressModal()" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>
        <form id="edit-address-form" action="" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên người nhận <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="receiver_name" id="edit_receiver_name" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Số điện thoại <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone" id="edit_phone" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Địa chỉ <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" id="edit_address" required rows="2"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" id="edit_is_default" value="1"
                        class="w-4 h-4 accent-red-500 cursor-pointer">
                    <label for="edit_is_default" class="text-sm text-gray-600 cursor-pointer">
                        Đặt làm địa chỉ mặc định
                    </label>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="flex-1 bg-red-500 text-white py-2.5 rounded-lg hover:bg-red-600 transition text-sm font-medium">
                    💾 Cập nhật
                </button>
                <button type="button" onclick="closeEditAddressModal()"
                    class="flex-1 border border-gray-200 py-2.5 rounded-lg hover:bg-gray-50 transition text-sm">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<!-- FOOTER -->
<footer class="bg-gray-900 text-white py-8 text-center">
    <p>© 2026 FoodShop - Laravel Project</p>
</footer>

<script>
// ── Tab switching ─────────────────────────────────────────────
function switchTab(name, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

(function () {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    const tabNames = ['info', 'orders', 'points', 'address', 'security'];
    if (tab && tabNames.includes(tab)) {
        const btn = document.querySelector(`.tab-btn[onclick*="'${tab}'"]`);
        if (btn) switchTab(tab, btn);
        else document.querySelector('.tab-btn').click();
    } else {
        document.querySelector('.tab-btn').classList.add('active');
        document.getElementById('tab-info').classList.add('active');
    }
})();

// ── Modal chỉnh sửa thông tin ────────────────────────────────
function openEditModal() {
    document.getElementById('edit-modal').classList.remove('hidden');
    document.getElementById('edit-modal').classList.add('flex');
}
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
    document.getElementById('edit-modal').classList.remove('flex');
}
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// ── Modal thêm địa chỉ ───────────────────────────────────────
function openAddressModal() {
    document.getElementById('address-modal').classList.remove('hidden');
    document.getElementById('address-modal').classList.add('flex');
}
function closeAddressModal() {
    document.getElementById('address-modal').classList.add('hidden');
    document.getElementById('address-modal').classList.remove('flex');
}
document.getElementById('address-modal').addEventListener('click', function(e) {
    if (e.target === this) closeAddressModal();
});

// ── Modal sửa địa chỉ ────────────────────────────────────────
function openEditAddressModal(id, receiverName, phone, address, isDefault) {
    document.getElementById('edit_receiver_name').value = receiverName;
    document.getElementById('edit_phone').value         = phone;
    document.getElementById('edit_address').value       = address;
    document.getElementById('edit_is_default').checked  = isDefault;
    document.getElementById('edit-address-form').action = '/address/' + id + '/update';
    document.getElementById('edit-address-modal').classList.remove('hidden');
    document.getElementById('edit-address-modal').classList.add('flex');
}
function closeEditAddressModal() {
    document.getElementById('edit-address-modal').classList.add('hidden');
    document.getElementById('edit-address-modal').classList.remove('flex');
}
document.getElementById('edit-address-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditAddressModal();
});

// ── Lọc đơn hàng ─────────────────────────────────────────────
function filterOrders(status) {
    document.querySelectorAll('.order-item').forEach(el => {
        el.style.display = (status === 'all' || el.dataset.status === status) ? '' : 'none';
    });
}

// ═══════════════════════════════════════════════════════════════
// ── AVATAR UPLOAD ─────────────────────────────────────────────
// ═══════════════════════════════════════════════════════════════

let selectedFile = null;  // lưu file người dùng chọn

function openAvatarModal() {
    const backdrop = document.getElementById('avatar-modal-backdrop');
    backdrop.classList.add('flex');
}

function closeAvatarModal() {
    const backdrop = document.getElementById('avatar-modal-backdrop');
    backdrop.classList.remove('flex');
    // Reset trạng thái nếu chưa lưu
    cancelAvatarSelect();
}

function handleBackdropClick(e) {
    if (e.target === document.getElementById('avatar-modal-backdrop')) {
        closeAvatarModal();
    }
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    const errorEl = document.getElementById('avatar-error');
    errorEl.classList.add('hidden');

    if (!file) return;

    // Validate loại file
    if (!file.type.startsWith('image/')) {
        showAvatarError('Vui lòng chọn file ảnh (JPG, PNG, GIF).');
        return;
    }

    // Validate dung lượng (2MB)
    if (file.size > 2 * 1024 * 1024) {
        showAvatarError('Ảnh quá lớn. Vui lòng chọn ảnh nhỏ hơn 2MB.');
        return;
    }

    selectedFile = file;

    // Hiển thị preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewRing = document.getElementById('avatar-preview-ring');

        // Xóa nội dung cũ (initials hoặc img cũ)
        previewRing.innerHTML = '';

        // Thêm img preview mới
        const img = document.createElement('img');
        img.src = e.target.result;
        img.id  = 'avatar-preview-img';
        img.className = 'w-full h-full object-cover';
        previewRing.appendChild(img);

        // Hiện nút hành động
        document.getElementById('avatar-actions').classList.remove('hidden');
        document.getElementById('avatar-actions').classList.add('flex');
    };
    reader.readAsDataURL(file);
}

function showAvatarError(msg) {
    const errorEl = document.getElementById('avatar-error');
    errorEl.textContent = msg;
    errorEl.classList.remove('hidden');
}

function cancelAvatarSelect() {
    selectedFile = null;
    document.getElementById('avatar-file-input').value = '';
    document.getElementById('avatar-actions').classList.add('hidden');
    document.getElementById('avatar-actions').classList.remove('flex');
    document.getElementById('avatar-error').classList.add('hidden');

    // Khôi phục preview về ảnh hiện tại (hoặc initials)
    const previewRing = document.getElementById('avatar-preview-ring');
    const currentSrc  = document.getElementById('avatar-display')?.src;

    previewRing.innerHTML = '';

    if (currentSrc && !currentSrc.endsWith('/')) {
        // Đang có ảnh thật → restore img
        const img = document.createElement('img');
        img.src       = currentSrc;
        img.id        = 'avatar-preview-img';
        img.className = 'w-full h-full object-cover';
        previewRing.appendChild(img);
    } else {
        // Chưa có ảnh → restore initials
        const span = document.createElement('span');
        span.id        = 'avatar-preview-initials';
        span.className = 'text-5xl font-bold text-red-400';
        span.textContent = document.querySelector('.avatar-initials')?.textContent?.trim()
                         || document.querySelector('h1')?.textContent?.trim()?.[0]?.toUpperCase()
                         || 'U';
        previewRing.appendChild(span);
    }
}

function submitAvatarUpload() {
    if (!selectedFile) return;

    const spinner  = document.getElementById('avatar-spinner');
    const errorEl  = document.getElementById('avatar-error');
    const actionsEl = document.getElementById('avatar-actions');
    errorEl.classList.add('hidden');

    // Hiện spinner
    spinner.classList.remove('hidden');
    spinner.classList.add('flex');
    actionsEl.querySelectorAll('button').forEach(b => b.disabled = true);

    // Tạo FormData
    const formData = new FormData();
    formData.append('avatar', selectedFile);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content
                              || '{{ csrf_token() }}');

    fetch('/profile/avatar', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Cập nhật avatar trên trang (header + navbar nếu có)
            const newSrc = data.avatar_url + '?t=' + Date.now();

            // Thay avatar ở header profile
            const displayEl = document.getElementById('avatar-display');
            if (displayEl) {
                if (displayEl.tagName === 'IMG') {
                    displayEl.src = newSrc;
                } else {
                    // Đang là div initials → thay bằng img
                    const img = document.createElement('img');
                    img.id        = 'avatar-display';
                    img.src       = newSrc;
                    img.alt       = 'Avatar';
                    img.className = 'avatar-img w-24 h-24 rounded-full object-cover border-4 border-white shadow-md';
                    displayEl.replaceWith(img);
                }
            }

            closeAvatarModal();
        } else {
            showAvatarError(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    })
    .catch(() => {
        showAvatarError('Không thể kết nối máy chủ. Vui lòng thử lại.');
    })
    .finally(() => {
        spinner.classList.add('hidden');
        spinner.classList.remove('flex');
        actionsEl.querySelectorAll('button').forEach(b => b.disabled = false);
        selectedFile = null;
        document.getElementById('avatar-file-input').value = '';
        document.getElementById('avatar-actions').classList.add('hidden');
        document.getElementById('avatar-actions').classList.remove('flex');
    });
}

function removeAvatar() {
    if (!confirm('Bạn chắc chắn muốn xóa ảnh đại diện?')) return;

    fetch('/profile/avatar/remove', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Lấy initial từ tên
            const initial = '{{ strtoupper(substr(session("user")["name"] ?? "U", 0, 1)) }}';

            // Thay avatar display về initials
            const displayEl = document.getElementById('avatar-display');
            if (displayEl) {
                const div = document.createElement('div');
                div.id        = 'avatar-display';
                div.className = 'avatar-initials w-24 h-24 rounded-full bg-red-100 flex items-center justify-center text-4xl font-bold text-red-500 border-4 border-white shadow-md';
                div.textContent = initial;
                displayEl.replaceWith(div);
            }

            closeAvatarModal();
        }
    })
    .catch(() => {
        showAvatarError('Không thể xóa ảnh. Vui lòng thử lại.');
    });
}
</script>
</body>
</html>