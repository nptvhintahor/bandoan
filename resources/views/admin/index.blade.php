@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-sub', 'Chào buổi sáng! Đây là tổng quan hôm nay.')

@section('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 1400px) {
        .kpi-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 900px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .kpi-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 22px 22px 18px;
        position: relative;
        overflow: hidden;
        transition: all 0.25s;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.07); }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 16px 16px 0 0;
    }
    .kpi-card.brand::before { background: var(--brand); }
    .kpi-card.green::before { background: var(--green); }
    .kpi-card.blue::before  { background: var(--blue); }
    .kpi-card.amber::before { background: var(--amber); }
    .kpi-card.indigo::before { background: #6366F1; }

    .kpi-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; }
    .kpi-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 17px; }
    .kpi-card.brand .kpi-icon { background: var(--brand-lt); color: var(--brand); }
    .kpi-card.green .kpi-icon { background: var(--green-lt); color: var(--green); }
    .kpi-card.blue  .kpi-icon { background: var(--blue-lt);  color: var(--blue); }
    .kpi-card.amber .kpi-icon { background: var(--amber-lt); color: var(--amber); }
    .kpi-card.indigo .kpi-icon { background: #EEF2FF; color: #6366F1; }

    .kpi-badge { font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 100px; display: flex; align-items: center; gap: 4px; }
    .kpi-badge.up      { background: var(--green-lt); color: var(--green); }
    .kpi-badge.down    { background: var(--red-lt);   color: #DC2626; }
    .kpi-badge.neutral { background: var(--surface);  color: var(--ink-3); }

    .kpi-value { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; color: var(--ink); line-height: 1; margin-bottom: 5px; letter-spacing: -0.5px; }
    .kpi-label { font-size: 12.5px; color: var(--ink-3); font-weight: 500; }
    .kpi-sub { font-size: 11.5px; color: var(--ink-3); margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border); display: flex; align-items: center; gap: 5px; }
    .kpi-sub i { font-size: 10px; }

    /* ── Chat KPI Card ── */
    .kpi-card.indigo {
        text-decoration: none;
        display: block;
        cursor: pointer;
    }
    .kpi-card.indigo .kpi-value {
        color: #6366F1;
    }

    .chat-unread-badge {
        background: #EF4444;
        color: white;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 100px;
        display: none;
        align-items: center;
        animation: pulse-badge 1.8s ease-in-out infinite;
    }
    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
        50%       { transform: scale(1.08); box-shadow: 0 0 0 5px rgba(239,68,68,0); }
    }

    .chat-status-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
        transition: background 0.3s;
    }
    .chat-status-dot.has-unread {
        background: #EF4444;
        animation: blink-dot 1.4s ease-in-out infinite;
    }
    .chat-status-dot.no-unread {
        background: var(--green);
    }
    @keyframes blink-dot {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.25; }
    }

    .chat-card-arrow {
        font-size: 10px;
        color: #6366F1;
        opacity: 0;
        transform: translateX(-4px);
        transition: all 0.2s;
    }
    .kpi-card.indigo:hover .chat-card-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    /* ── Chart row ── */
    .chart-row { display: grid; grid-template-columns: 1.65fr 1fr; gap: 16px; margin-bottom: 24px; }
    .chart-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 22px 24px; }
    .chart-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
    .chart-title { font-size: 14.5px; font-weight: 600; color: var(--ink); margin-bottom: 2px; }
    .chart-sub { font-size: 12px; color: var(--ink-3); }
    .chart-tabs { display: flex; gap: 4px; background: var(--surface); padding: 3px; border-radius: 8px; }
    .chart-tab { font-size: 11.5px; font-weight: 600; padding: 5px 12px; border-radius: 6px; border: none; cursor: pointer; color: var(--ink-3); background: transparent; font-family: 'DM Sans', sans-serif; transition: all 0.18s; }
    .chart-tab.active { background: var(--card); color: var(--ink); box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
    .chart-wrap { position: relative; height: 240px; }

    .donut-wrap { position: relative; height: 200px; }
    .donut-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none; }
    .donut-center-val { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: var(--ink); line-height: 1; }
    .donut-center-lbl { font-size: 11px; color: var(--ink-3); margin-top: 3px; }
    .donut-legend { margin-top: 16px; display: flex; flex-direction: column; gap: 10px; }
    .legend-item { display: flex; align-items: center; justify-content: space-between; font-size: 13px; }
    .legend-left { display: flex; align-items: center; gap: 8px; }
    .legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
    .legend-name { color: var(--ink-2); font-weight: 500; }
    .legend-val   { color: var(--ink); font-weight: 600; }

    /* ── Bottom row ── */
    .bottom-row { display: grid; grid-template-columns: 1.4fr 1fr; gap: 16px; }

    .table-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .table-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--border); }
    .table-title { font-size: 14.5px; font-weight: 600; color: var(--ink); }
    .table-link { font-size: 12.5px; color: var(--brand); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 4px; transition: gap 0.18s; }
    .table-link:hover { gap: 7px; }

    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: var(--ink-3); padding: 10px 22px; text-align: left; background: var(--surface); border-bottom: 1px solid var(--border); }
    tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #FDFAF8; }
    tbody td { padding: 13px 22px; font-size: 13.5px; color: var(--ink); }

    .td-food { display: flex; align-items: center; gap: 10px; }
    .td-img  { width: 38px; height: 38px; border-radius: 10px; object-fit: cover; border: 1px solid var(--border); flex-shrink: 0; }
    .td-img-placeholder { width: 38px; height: 38px; border-radius: 10px; background: var(--surface); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--ink-3); font-size: 14px; flex-shrink: 0; }
    .td-name { font-weight: 600; font-size: 13px; color: var(--ink); }
    .td-id   { font-size: 11px; color: var(--ink-3); margin-top: 1px; }
    .td-price { font-weight: 700; color: var(--brand); }

    .badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; padding: 4px 10px; border-radius: 100px; }
    .badge-green { background: var(--green-lt); color: var(--green); }
    .badge-amber { background: var(--amber-lt); color: var(--amber); }
    .badge-red   { background: var(--red-lt);   color: #DC2626; }

    .activity-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .activity-list { padding: 8px 0; }
    .activity-item { display: flex; align-items: flex-start; gap: 12px; padding: 13px 20px; transition: background 0.15s; }
    .activity-item:hover { background: #FDFAF8; }
    .activity-icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; margin-top: 1px; }
    .activity-body { flex: 1; min-width: 0; }
    .activity-text { font-size: 13px; color: var(--ink); line-height: 1.45; }
    .activity-text strong { font-weight: 600; }
    .activity-time { font-size: 11.5px; color: var(--ink-3); margin-top: 3px; display: flex; align-items: center; gap: 4px; }
    .activity-divider { height: 1px; background: var(--border); margin: 0 20px; }

    .progress-section { padding: 0 4px; }
    .progress-item { margin-bottom: 16px; }
    .progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px; }
    .progress-label { font-size: 13px; font-weight: 500; color: var(--ink-2); }
    .progress-val   { font-size: 13px; font-weight: 700; color: var(--ink); }
    .progress-track { height: 7px; background: var(--surface); border-radius: 100px; overflow: hidden; }
    .progress-fill  { height: 100%; border-radius: 100px; transition: width 1s ease; }
</style>
@endsection

@section('content')

{{-- ── KPI CARDS ─────────────────────────────────────────────────────── --}}
<div class="kpi-grid">

    {{-- Doanh thu --}}
    <div class="kpi-card brand">
        <div class="kpi-top">
            <div class="kpi-icon"><i class="fas fa-fire-alt"></i></div>
            @if($revenueGrowth > 0)
                <div class="kpi-badge up"><i class="fas fa-arrow-up"></i> +{{ $revenueGrowth }}%</div>
            @elseif($revenueGrowth < 0)
                <div class="kpi-badge down"><i class="fas fa-arrow-down"></i> {{ $revenueGrowth }}%</div>
            @else
                <div class="kpi-badge neutral"><i class="fas fa-minus"></i> Mới</div>
            @endif
        </div>
        <div class="kpi-value">{{ number_format($totalRevenue) }}đ</div>
        <div class="kpi-label">Doanh thu tháng này</div>
        <div class="kpi-sub">
            <i class="far fa-clock"></i>
            So với tháng trước:
            @if($revenueDiff >= 0)
                +{{ number_format($revenueDiff / 1000000, 1) }} triệu
            @else
                {{ number_format($revenueDiff / 1000000, 1) }} triệu
            @endif
        </div>
    </div>

    {{-- Đơn hàng --}}
    <div class="kpi-card green">
        <div class="kpi-top">
            <div class="kpi-icon"><i class="fas fa-shopping-bag"></i></div>
            @if($ordersGrowth > 0)
                <div class="kpi-badge up"><i class="fas fa-arrow-up"></i> +{{ $ordersGrowth }}%</div>
            @elseif($ordersGrowth < 0)
                <div class="kpi-badge down"><i class="fas fa-arrow-down"></i> {{ $ordersGrowth }}%</div>
            @else
                <div class="kpi-badge neutral"><i class="fas fa-minus"></i> Mới</div>
            @endif
        </div>
        <div class="kpi-value">{{ number_format($totalOrders) }}</div>
        <div class="kpi-label">Đơn hàng tháng này</div>
        <div class="kpi-sub"><i class="far fa-clock"></i> Hôm nay: <strong style="color:var(--ink);margin:0 2px">{{ $todayOrders }}</strong> đơn mới</div>
    </div>

    {{-- Khách hàng --}}
    <div class="kpi-card blue">
        <div class="kpi-top">
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
            @if($usersGrowth > 0)
                <div class="kpi-badge up"><i class="fas fa-arrow-up"></i> +{{ $usersGrowth }}%</div>
            @elseif($usersGrowth < 0)
                <div class="kpi-badge down"><i class="fas fa-arrow-down"></i> {{ $usersGrowth }}%</div>
            @else
                <div class="kpi-badge neutral"><i class="fas fa-minus"></i> Mới</div>
            @endif
        </div>
        <div class="kpi-value">{{ number_format($totalUsers) }}</div>
        <div class="kpi-label">Khách hàng</div>
        <div class="kpi-sub"><i class="fas fa-user-plus"></i> +{{ $newUsers }} người dùng mới tuần này</div>
    </div>

    {{-- Món ăn --}}
    <div class="kpi-card amber">
        <div class="kpi-top">
            <div class="kpi-icon"><i class="fas fa-hamburger"></i></div>
            <div class="kpi-badge neutral"><i class="fas fa-layer-group"></i> {{ $totalFoods + $hiddenFoods }} tổng</div>
        </div>
        <div class="kpi-value">{{ $totalFoods }}</div>
        <div class="kpi-label">Món ăn đang bán</div>
        <div class="kpi-sub"><i class="fas fa-eye-slash"></i> {{ $hiddenFoods }} món đang ẩn</div>
    </div>

    {{-- Tin nhắn (luôn hiển thị, có badge thông báo) --}}
    <a href="/admin/chat" class="kpi-card indigo">
        <div class="kpi-top">
            <div class="kpi-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
            </div>
            <span id="chatUnreadBadge" class="chat-unread-badge">0</span>
        </div>
        <div class="kpi-value" id="chatUnreadVal">—</div>
        <div class="kpi-label">Tin nhắn chưa đọc</div>
        <div class="kpi-sub">
            <span id="chatStatusDot" class="chat-status-dot no-unread"></span>
            <span id="chatSubText">Đang kiểm tra...</span>
            <i class="fas fa-arrow-right chat-card-arrow" style="margin-left:auto"></i>
        </div>
    </a>

</div>

{{-- ── CHART ROW ──────────────────────────────────────────────────────── --}}
<div class="chart-row">

    {{-- Doanh thu theo thời gian --}}
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Doanh thu theo thời gian</div>
                <div class="chart-sub">Dữ liệu thực tế từ database</div>
            </div>
            <div class="chart-tabs">
                <button class="chart-tab active" onclick="switchChart('7days',this)">7 ngày</button>
                <button class="chart-tab" onclick="switchChart('30days',this)">30 ngày</button>
            </div>
        </div>
        <div class="chart-wrap"><canvas id="revenueChart"></canvas></div>
    </div>

    {{-- Cơ cấu đơn hàng --}}
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Cơ cấu đơn hàng</div>
                <div class="chart-sub">Theo trạng thái tháng này</div>
            </div>
        </div>
        <div class="donut-wrap">
            <canvas id="donutChart"></canvas>
            <div class="donut-center">
                <div class="donut-center-val">{{ number_format($totalOrders) }}</div>
                <div class="donut-center-lbl">Tổng đơn</div>
            </div>
        </div>
        <div class="donut-legend">
            <div class="legend-item">
                <div class="legend-left"><div class="legend-dot" style="background:#16A34A"></div><span class="legend-name">Hoàn thành</span></div>
                <span class="legend-val">{{ $statusMap['Hoàn thành'] }} đơn</span>
            </div>
            <div class="legend-item">
                <div class="legend-left"><div class="legend-dot" style="background:#E8501A"></div><span class="legend-name">Đang giao</span></div>
                <span class="legend-val">{{ $statusMap['Đang giao'] }} đơn</span>
            </div>
            <div class="legend-item">
                <div class="legend-left"><div class="legend-dot" style="background:#F59E0B"></div><span class="legend-name">Chờ xác nhận</span></div>
                <span class="legend-val">{{ $statusMap['Chờ xác nhận'] }} đơn</span>
            </div>
            <div class="legend-item">
                <div class="legend-left"><div class="legend-dot" style="background:#E5E7EB"></div><span class="legend-name">Đã hủy</span></div>
                <span class="legend-val">{{ $statusMap['Đã hủy'] }} đơn</span>
            </div>
        </div>
    </div>

</div>

{{-- ── BOTTOM ROW ─────────────────────────────────────────────────────── --}}
<div class="bottom-row">

    {{-- Top món ăn bán chạy --}}
    <div class="table-card">
        <div class="table-head">
            <span class="table-title">Top món ăn bán chạy</span>
            <a href="/admin/foods" class="table-link">Xem tất cả <i class="fas fa-arrow-right" style="font-size:10px"></i></a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Món ăn</th>
                    <th>Giá</th>
                    <th>Đã bán</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topFoodList as $i => $item)
                @php $food = $item['food']; @endphp
                <tr>
                    <td style="color:var(--ink-3);font-weight:700;font-size:13px">
                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td>
                        <div class="td-food">
                            @if($food && $food->image)
                                <img src="{{ $food->image }}" class="td-img" alt="{{ $item['name'] }}"
                                     onerror="this.outerHTML='<div class=\'td-img-placeholder\'><i class=\'fas fa-image\'></i></div>'">
                            @else
                                <div class="td-img-placeholder"><i class="fas fa-utensils"></i></div>
                            @endif
                            <div>
                                <div class="td-name">{{ $item['name'] }}</div>
                                <div class="td-id">#{{ $food ? str_pad($food->id, 3, '0', STR_PAD_LEFT) : '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="td-price">
                        {{ $food ? number_format($food->price) . 'đ' : '—' }}
                    </td>
                    <td><strong>{{ number_format($item['sold']) }}</strong></td>
                    <td>
                        @if(!$food)
                            <span class="badge badge-red"><i class="fas fa-circle" style="font-size:7px"></i> Đã xóa</span>
                        @elseif($food->is_active)
                            <span class="badge badge-green"><i class="fas fa-circle" style="font-size:7px"></i> Đang bán</span>
                        @else
                            <span class="badge badge-amber"><i class="fas fa-circle" style="font-size:7px"></i> Đang ẩn</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:32px;color:var(--ink-3);font-size:13px">
                        <i class="fas fa-inbox" style="font-size:20px;margin-bottom:8px;display:block"></i>
                        Chưa có dữ liệu bán hàng tháng này
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px">

        {{-- Danh mục bán chạy --}}
        <div class="chart-card">
            <div class="chart-header" style="margin-bottom:14px">
                <div>
                    <div class="chart-title">Danh mục bán chạy</div>
                    <div class="chart-sub">% doanh số theo loại món</div>
                </div>
            </div>
            <div class="progress-section">
                @foreach($categoryData as $cat)
                <div class="progress-item" @if($loop->last) style="margin-bottom:0" @endif>
                    <div class="progress-header">
                        <span class="progress-label">{{ $cat['label'] }}</span>
                        <span class="progress-val">{{ $cat['pct'] }}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width:{{ $cat['pct'] }}%;background:{{ $cat['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Hoạt động gần đây --}}
        <div class="activity-card">
            <div class="table-head">
                <span class="table-title">Hoạt động gần đây</span>
                <a href="/admin/orders" class="table-link">Xem đơn <i class="fas fa-arrow-right" style="font-size:10px"></i></a>
            </div>
            <div class="activity-list">
                @forelse($recentOrders as $i => $order)
                @php
                    $items     = is_string($order->items) ? json_decode($order->items, true) : ($order->items ?? []);
                    $itemNames = collect($items)->map(fn($it) => ($it['quantity'] ?? 1).'× '.($it['name'] ?? ''))->take(2)->implode(', ');
                    $iconMap   = [
                        'Hoàn thành'   => ['icon'=>'fa-check',        'bg'=>'var(--green-lt)', 'color'=>'var(--green)'],
                        'Đang giao'    => ['icon'=>'fa-truck',         'bg'=>'var(--blue-lt)',  'color'=>'var(--blue)'],
                        'Đang xử lý'   => ['icon'=>'fa-spinner',       'bg'=>'var(--amber-lt)', 'color'=>'var(--amber)'],
                        'Chờ xác nhận' => ['icon'=>'fa-shopping-bag',  'bg'=>'var(--brand-lt)', 'color'=>'var(--brand)'],
                        'Đã hủy'       => ['icon'=>'fa-times-circle',  'bg'=>'var(--red-lt)',   'color'=>'#DC2626'],
                    ];
                    $ic = $iconMap[$order->status] ?? $iconMap['Chờ xác nhận'];
                @endphp
                @if($i > 0)<div class="activity-divider"></div>@endif
                <div class="activity-item">
                    <div class="activity-icon" style="background:{{ $ic['bg'] }};color:{{ $ic['color'] }}">
                        <i class="fas {{ $ic['icon'] }}"></i>
                    </div>
                    <div class="activity-body">
                        <div class="activity-text">
                            <strong>Đơn #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            @if($order->status === 'Hoàn thành')
                                đã giao thành công
                            @elseif($order->status === 'Đã hủy')
                                đã bị hủy
                            @else
                                — {{ $itemNames }}
                            @endif
                        </div>
                        <div class="activity-time">
                            <i class="far fa-clock"></i>
                            {{ $order->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div style="padding:24px;text-align:center;color:var(--ink-3);font-size:13px">
                    Chưa có hoạt động nào
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
// Dữ liệu từ DB — truyền qua Blade
const data7 = {
    labels:  {!! json_encode($labels7) !!},
    revenue: {!! json_encode($revenue7) !!},
    orders:  {!! json_encode($orders7) !!}
};
const data30 = {
    labels:  {!! json_encode($labels30) !!},
    revenue: {!! json_encode($revenue30) !!},
    orders:  {!! json_encode($orders30) !!}
};
const statusMap = {!! json_encode($statusMap) !!};

// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    data: {
        labels: data7.labels,
        datasets: [
            {
                type: 'line', label: 'Doanh thu', data: data7.revenue,
                borderColor: '#E8501A', backgroundColor: 'rgba(232,80,26,0.08)',
                borderWidth: 2.5, pointRadius: 4,
                pointBackgroundColor: '#E8501A', pointBorderColor: '#fff', pointBorderWidth: 2,
                tension: 0.4, fill: true, yAxisID: 'y'
            },
            {
                type: 'bar', label: 'Đơn hàng', data: data7.orders,
                backgroundColor: 'rgba(29,111,190,0.12)', borderColor: 'rgba(29,111,190,0.35)',
                borderWidth: 1, borderRadius: 5, yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1C1008', titleColor: 'rgba(255,255,255,0.6)',
                bodyColor: '#fff', padding: 10, cornerRadius: 8,
                callbacks: {
                    label: c => c.dataset.label === 'Doanh thu'
                        ? ` ${(c.parsed.y / 1e6).toFixed(1)}tr đ`
                        : ` ${c.parsed.y} đơn`
                }
            }
        },
        scales: {
            x:  { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11, family: 'DM Sans' }, color: '#9A7860' } },
            y:  { position: 'left',  grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false, dash: [4,4] }, ticks: { font: { size: 11, family: 'DM Sans' }, color: '#9A7860', callback: v => (v/1e6).toFixed(0) + 'tr' } },
            y1: { position: 'right', grid: { display: false }, border: { display: false }, ticks: { font: { size: 11, family: 'DM Sans' }, color: '#1D6FBE', callback: v => v + ' đơn' } }
        }
    }
});

function switchChart(range, btn) {
    document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const d = range === '7days' ? data7 : data30;
    revenueChart.data.labels = d.labels;
    revenueChart.data.datasets[0].data = d.revenue;
    revenueChart.data.datasets[1].data = d.orders;
    revenueChart.update();
}

// Donut Chart — dữ liệu thực từ DB
new Chart(document.getElementById('donutChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusMap),
        datasets: [{
            data: Object.values(statusMap),
            backgroundColor: ['#16A34A', '#E8501A', '#F59E0B', '#E5E7EB'],
            borderWidth: 0, hoverOffset: 6
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1C1008', titleColor: 'rgba(255,255,255,0.6)',
                bodyColor: '#fff', padding: 10, cornerRadius: 8,
                callbacks: { label: c => ` ${c.label}: ${c.parsed} đơn` }
            }
        }
    }
});

// ── Polling unread count — cập nhật KPI card Chat + topbar ──
async function checkAdminUnread() {
    try {
        const res   = await fetch('/admin/chat/unread-count');
        const data  = await res.json();
        const count = data.count || 0;

        // KPI card elements
        const badge   = document.getElementById('chatUnreadBadge');
        const val     = document.getElementById('chatUnreadVal');
        const dot     = document.getElementById('chatStatusDot');
        const subText = document.getElementById('chatSubText');

        // Cập nhật số đếm trong card
        if (val) val.textContent = count;

        // Badge đỏ nhấp nháy — chỉ hiện khi có tin chưa đọc
        if (badge) {
            badge.textContent   = count;
            badge.style.display = count > 0 ? 'inline-flex' : 'none';
        }

        // Dot trạng thái
        if (dot) {
            dot.className = 'chat-status-dot ' + (count > 0 ? 'has-unread' : 'no-unread');
        }

        // Sub text
        if (subText) {
            subText.textContent = count > 0
                ? `${count} khách đang chờ phản hồi`
                : 'Không có tin nhắn mới';
            subText.style.color = count > 0 ? '#6366F1' : 'var(--ink-3)';
        }

        // Topbar pill (nếu layout admin có id="adminUnreadPill")
        const pill = document.getElementById('adminUnreadPill');
        if (pill) {
            pill.textContent   = count;
            pill.style.display = count > 0 ? 'inline-flex' : 'none';
        }

        // Topbar dot (nếu layout admin có id="adminChatDot")
        const topDot = document.getElementById('adminChatDot');
        if (topDot) topDot.style.display = count > 0 ? 'block' : 'none';

    } catch(e) {
        const subText = document.getElementById('chatSubText');
        if (subText) {
            subText.textContent = 'Không thể kết nối';
            subText.style.color = '#DC2626';
        }
    }
}

checkAdminUnread();
setInterval(checkAdminUnread, 5000);
</script>
@endsection