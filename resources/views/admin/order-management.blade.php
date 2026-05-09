@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')
@section('page-title', 'Quản lý đơn hàng')
@section('page-sub', 'Theo dõi và cập nhật trạng thái tất cả đơn hàng')

@section('head')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .stat-label { font-size: 11.5px; font-weight: 600; color: var(--ink-3); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .stat-val   { font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 700; color: var(--ink); line-height: 1; }
    .stat-val.amber { color: #D97706; }
    .stat-val.blue  { color: var(--blue); }
    .stat-val.green { color: var(--green); }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }

    .orders-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .orders-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); }
    .orders-title { font-size: 14.5px; font-weight: 600; color: var(--ink); }
    .orders-count { font-size: 12.5px; color: var(--ink-3); }

    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: var(--ink-3); padding: 11px 20px; text-align: left; background: var(--surface); border-bottom: 1px solid var(--border); }
    tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #FDFAF8; }
    tbody td { padding: 14px 20px; font-size: 13.5px; color: var(--ink); vertical-align: middle; }

    .order-id { font-weight: 700; color: var(--ink); font-size: 13.5px; }
    .order-date { font-size: 11px; color: var(--ink-3); margin-top: 2px; }

    .customer-avatar { width: 32px; height: 32px; background: var(--brand-lt); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--brand); flex-shrink: 0; }
    .customer-name  { font-size: 13px; font-weight: 600; color: var(--ink); }
    .customer-email { font-size: 11px; color: var(--ink-3); margin-top: 1px; }

    .items-list { font-size: 12.5px; color: var(--ink-2); line-height: 1.6; }
    .items-more { font-size: 11.5px; color: var(--ink-3); font-style: italic; }

    .price { font-weight: 700; color: var(--ink); }
    .price-unit { font-size: 11px; font-weight: 400; color: var(--ink-3); }

    .pay-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; padding: 4px 10px; border-radius: 8px; }
    .pay-cod    { background: var(--surface); color: var(--ink-3); }
    .pay-online { background: var(--blue-lt); color: var(--blue); }

    .status-badge { display: inline-block; font-size: 11.5px; font-weight: 600; padding: 5px 11px; border-radius: 8px; }
    .status-cho    { background: #FEF3C7; color: #D97706; }
    .status-xu-ly  { background: #FFEDD5; color: #EA580C; }
    .status-giao   { background: var(--blue-lt); color: var(--blue); }
    .status-xong   { background: var(--green-lt); color: var(--green); }
    .status-huy    { background: var(--red-lt); color: #DC2626; }

    .btn-update {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 600;
        padding: 7px 14px; border-radius: 8px;
        background: var(--surface); color: var(--ink-2);
        border: 1px solid var(--border);
        cursor: pointer; transition: all 0.18s;
        font-family: 'DM Sans', sans-serif;
    }
    .btn-update:hover { background: var(--ink); color: #fff; border-color: var(--ink); }

    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-icon { width: 60px; height: 60px; background: var(--surface); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 24px; color: var(--ink-3); }

    /* Modal */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 100; backdrop-filter: blur(4px); align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .modal-box { background: var(--card); border-radius: 20px; width: 100%; max-width: 380px; margin: 16px; padding: 28px; box-shadow: 0 24px 64px rgba(0,0,0,0.15); }
    .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
    .modal-title { font-size: 15px; font-weight: 700; color: var(--ink); }
    .modal-close { width: 30px; height: 30px; border-radius: 8px; border: none; background: var(--surface); cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--ink-3); font-size: 13px; transition: all 0.15s; }
    .modal-close:hover { background: var(--border); }
    .modal-sub { font-size: 12.5px; color: var(--ink-3); margin-bottom: 18px; }
    .modal-sub strong { color: var(--ink); font-weight: 600; }

    .status-option { display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 10px; border: 1.5px solid var(--border); cursor: pointer; margin-bottom: 8px; transition: all 0.15s; }
    .status-option:hover { border-color: var(--brand); background: var(--brand-lt); }
    .status-option:has(input:checked) { border-color: var(--brand); background: var(--brand-lt); }
    .status-option input { accent-color: var(--brand); }
    .status-option span { font-size: 13px; font-weight: 500; color: var(--ink); }

    .btn-save { width: 100%; background: var(--ink); color: white; font-weight: 700; font-size: 13.5px; padding: 13px; border-radius: 12px; border: none; cursor: pointer; font-family: 'DM Sans', sans-serif; margin-top: 6px; transition: background 0.18s; }
    .btn-save:hover { background: var(--brand); }

    .alert-success { display: flex; align-items: center; gap: 10px; background: var(--green-lt); border: 1px solid #BBF7D0; color: var(--green); padding: 13px 18px; border-radius: 12px; font-size: 13.5px; font-weight: 500; margin-bottom: 22px; }
</style>
@endsection

@section('content')

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

<!-- STATS -->
<div class="stats-grid">
    @php
        $total = $orders->count();
        $cho   = $orders->where('status', 'Chờ xác nhận')->count();
        $dang  = $orders->where('status', 'Đang giao')->count();
        $xong  = $orders->where('status', 'Hoàn thành')->count();
    @endphp
    <div class="stat-card">
        <div><div class="stat-label">Tổng đơn</div><div class="stat-val">{{ $total }}</div></div>
        <div class="stat-icon" style="background:var(--surface);color:var(--ink-3)"><i class="fas fa-layer-group"></i></div>
    </div>
    <div class="stat-card">
        <div><div class="stat-label">Chờ xác nhận</div><div class="stat-val amber">{{ $cho }}</div></div>
        <div class="stat-icon" style="background:var(--amber-lt);color:var(--amber)"><i class="fas fa-clock"></i></div>
    </div>
    <div class="stat-card">
        <div><div class="stat-label">Đang giao</div><div class="stat-val blue">{{ $dang }}</div></div>
        <div class="stat-icon" style="background:var(--blue-lt);color:var(--blue)"><i class="fas fa-truck"></i></div>
    </div>
    <div class="stat-card">
        <div><div class="stat-label">Hoàn thành</div><div class="stat-val green">{{ $xong }}</div></div>
        <div class="stat-icon" style="background:var(--green-lt);color:var(--green)"><i class="fas fa-check-circle"></i></div>
    </div>
</div>

<!-- TABLE -->
<div class="orders-card">
    <div class="orders-head">
        <span class="orders-title">Danh sách đơn hàng</span>
        <span class="orders-count">{{ $orders->count() }} đơn</span>
    </div>

    @if($orders->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
        <p style="font-size:14px;color:var(--ink-3);font-weight:500">Chưa có đơn hàng nào</p>
    </div>
    @else
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Đơn hàng</th>
                    <th>Khách hàng</th>
                    <th>Món đặt</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th style="text-align:right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                @php $items = json_decode($order->items, true); @endphp
                <tr>
                    <td>
                        <div class="order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <div class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="customer-avatar">{{ strtoupper(substr($order->user->name ?? 'K', 0, 1)) }}</div>
                            <div>
                                <div class="customer-name">{{ $order->user->name ?? 'Khách' }}</div>
                                <div class="customer-email">{{ $order->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="items-list">
                            @foreach(array_slice($items, 0, 2) as $item)
                                • {{ $item['name'] }} <span style="color:var(--ink-3)">x{{ $item['quantity'] }}</span><br>
                            @endforeach
                            @if(count($items) > 2)
                                <span class="items-more">+{{ count($items) - 2 }} món khác</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="price">{{ number_format($order->total) }}<span class="price-unit">đ</span></span>
                    </td>
                    <td>
                        @if($order->payment_method == 'cod')
                            <span class="pay-badge pay-cod"><i class="fas fa-money-bill-wave"></i> COD</span>
                        @else
                            <span class="pay-badge pay-online"><i class="fas fa-credit-card"></i> Online</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $cls = match($order->status) {
                                'Chờ xác nhận' => 'status-cho',
                                'Đang xử lý'   => 'status-xu-ly',
                                'Đang giao'    => 'status-giao',
                                'Hoàn thành'   => 'status-xong',
                                'Đã hủy'       => 'status-huy',
                                default        => 'status-cho',
                            };
                        @endphp
                        <span class="status-badge {{ $cls }}">{{ $order->status }}</span>
                    </td>
                    <td style="text-align:right">
                        <button class="btn-update"
                            onclick="openModal(this)"
                            data-id="{{ $order->id }}"
                            data-status="{{ $order->status }}">
                            <i class="fas fa-pen" style="font-size:11px"></i> Cập nhật
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- MODAL -->
<div id="statusModal" class="modal-overlay" onclick="closeModal(event)">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-header">
            <span class="modal-title">Cập nhật trạng thái</span>
            <button class="modal-close" onclick="document.getElementById('statusModal').classList.remove('active')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="modal-sub">Đơn hàng <strong id="modalOrderId"></strong></p>

        <form id="statusForm" method="POST">
            @csrf
            @method('PATCH')
            @foreach(['Chờ xác nhận','Đang xử lý','Đang giao','Hoàn thành','Đã hủy'] as $status)
            <label class="status-option">
                <input type="radio" name="status" value="{{ $status }}">
                <span>{{ $status }}</span>
            </label>
            @endforeach
            <button type="submit" class="btn-save">Lưu thay đổi</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openModal(btn) {
    const orderId = btn.dataset.id;
    const currentStatus = btn.dataset.status;
    document.getElementById('modalOrderId').textContent = '#' + String(orderId).padStart(4, '0');
    document.getElementById('statusForm').action = '/admin/orders/update-status/' + orderId;
    document.querySelectorAll('input[name="status"]').forEach(r => { r.checked = r.value === currentStatus; });
    document.getElementById('statusModal').classList.add('active');
}
function closeModal(e) {
    if (e.target === document.getElementById('statusModal'))
        document.getElementById('statusModal').classList.remove('active');
}
</script>
@endsection