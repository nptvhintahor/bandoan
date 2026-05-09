@extends('layouts.admin')

@section('title', 'Quản lý thực đơn')
@section('page-title', 'Quản lý thực đơn')
@section('page-sub', 'Danh sách tất cả các món ăn trên hệ thống')

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
    .stat-val.green  { color: var(--green); }
    .stat-val.gray   { color: var(--ink-3); }
    .stat-val.orange { color: #E8501A; }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }

    .foods-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .foods-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); }
    .foods-title { font-size: 14.5px; font-weight: 600; color: var(--ink); }

    .btn-add {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--ink); color: white;
        font-size: 13px; font-weight: 600;
        padding: 9px 18px; border-radius: 10px;
        text-decoration: none; transition: background 0.18s;
    }
    .btn-add:hover { background: var(--brand); }

    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: var(--ink-3); padding: 11px 20px; text-align: left; background: var(--surface); border-bottom: 1px solid var(--border); }
    tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #FDFAF8; }
    tbody td { padding: 13px 20px; font-size: 13.5px; color: var(--ink); vertical-align: middle; }

    .food-img { width: 52px; height: 52px; border-radius: 12px; object-fit: cover; border: 1px solid var(--border); }
    .food-img-placeholder { width: 52px; height: 52px; border-radius: 12px; background: var(--surface); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--ink-3); font-size: 18px; }
    .food-name { font-weight: 600; color: var(--ink); font-size: 13.5px; }
    .food-id   { font-size: 11px; color: var(--ink-3); margin-top: 2px; }
    .food-price { font-weight: 700; color: var(--ink); }
    .food-price-unit { font-size: 11px; font-weight: 400; color: var(--ink-3); }

    .toggle-btn {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 11.5px; font-weight: 600;
        padding: 5px 12px; border-radius: 8px;
        border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.18s;
    }
    .toggle-on  { background: var(--green-lt); color: var(--green); border: 1px solid #BBF7D0; }
    .toggle-on:hover  { background: #BBF7D0; }
    .toggle-off { background: var(--surface); color: var(--ink-3); border: 1px solid var(--border); }
    .toggle-off:hover { background: var(--border); }

    .action-btn {
        width: 34px; height: 34px;
        border-radius: 9px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px;
        text-decoration: none;
        transition: all 0.18s;
    }
    .action-edit   { background: var(--blue-lt); color: var(--blue); }
    .action-edit:hover { background: var(--blue); color: white; }
    .action-delete { background: var(--red-lt); color: #DC2626; }
    .action-delete:hover { background: #DC2626; color: white; }

    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-icon { width: 60px; height: 60px; background: var(--surface); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 24px; color: var(--ink-3); }

    .alert-success { display: flex; align-items: center; gap: 10px; background: var(--green-lt); border: 1px solid #BBF7D0; color: var(--green); padding: 13px 18px; border-radius: 12px; font-size: 13.5px; font-weight: 500; margin-bottom: 22px; }

    /* ===== VOUCHER (thêm mới) ===== */
    .voucher-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 700;
        padding: 4px 10px; border-radius: 8px;
        letter-spacing: 0.3px; white-space: nowrap;
    }
    .badge-hot  { background: #FFF1EB; color: #C03E0E; border: 1px solid #FCDACD; }
    .badge-sale { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
    .badge-none { background: var(--surface); color: var(--ink-3); border: 1px solid var(--border); }

    .voucher-price-val          { font-size: 13px; font-weight: 700; }
    .voucher-price-val.hot      { color: #C03E0E; }
    .voucher-price-val.sale     { color: #065F46; }

    .voucher-hint               { font-size: 10.5px; color: var(--ink-3); margin-top: 3px; line-height: 1.4; }
    .voucher-hint.hint-hot      { color: #C03E0E; }
    .voucher-hint.hint-sale     { color: #065F46; }

    .voucher-form-wrap          { display: flex; align-items: center; gap: 6px; margin-top: 8px; }

    .voucher-input {
        width: 130px; padding: 6px 10px;
        font-size: 12.5px; font-family: 'DM Sans', sans-serif;
        color: var(--ink); background: var(--surface);
        border: 1px solid var(--border); border-radius: 8px;
        outline: none; transition: border-color 0.18s;
    }
    .voucher-input:focus        { border-color: #E8501A; background: #fff; }
    .voucher-input::placeholder { color: var(--ink-3); }

    .btn-voucher-save {
        padding: 6px 11px; font-size: 11.5px; font-weight: 600;
        font-family: 'DM Sans', sans-serif; border: none;
        border-radius: 8px; cursor: pointer;
        background: #E8501A; color: white; transition: background 0.18s;
        white-space: nowrap;
    }
    .btn-voucher-save:hover     { background: #C03E0E; }

    .btn-voucher-clear {
        padding: 6px 9px; font-size: 11px; font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        border: 1px solid var(--border); border-radius: 8px;
        cursor: pointer; background: var(--surface); color: var(--ink-3);
        transition: all 0.18s;
    }
    .btn-voucher-clear:hover    { background: #fee2e2; color: #DC2626; border-color: #fca5a5; }
</style>
@endsection

@section('content')

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<!-- Header -->
<div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px">
    <div></div>
    <a href="/admin/foods/create" class="btn-add">
        <i class="fas fa-plus" style="font-size:11px"></i> Thêm món mới
    </a>
</div>

<!-- Stats — thêm ô Có voucher, đổi grid thành 4 cột -->
<div class="stats-grid">
    <div class="stat-card">
        <div><div class="stat-label">Tổng số món</div><div class="stat-val">{{ $foods->count() }}</div></div>
        <div class="stat-icon" style="background:var(--surface);color:var(--ink-3)"><i class="fas fa-layer-group"></i></div>
    </div>
    <div class="stat-card">
        <div><div class="stat-label">Đang hiện</div><div class="stat-val green">{{ $foods->where('is_active', 1)->count() }}</div></div>
        <div class="stat-icon" style="background:var(--green-lt);color:var(--green)"><i class="fas fa-eye"></i></div>
    </div>
    <div class="stat-card">
        <div><div class="stat-label">Đang ẩn</div><div class="stat-val gray">{{ $foods->where('is_active', 0)->count() }}</div></div>
        <div class="stat-icon" style="background:var(--surface);color:var(--ink-3)"><i class="fas fa-eye-slash"></i></div>
    </div>
    <div class="stat-card">
        <div><div class="stat-label">Có voucher</div><div class="stat-val orange">{{ $foods->whereNotNull('voucher_price')->count() }}</div></div>
        <div class="stat-icon" style="background:#FFF1EB;color:#E8501A"><i class="fas fa-tag"></i></div>
    </div>
</div>

<!-- Table -->
<div class="foods-card">
    <div class="foods-head">
        <span class="foods-title">Danh sách món ăn</span>
        <span style="font-size:12.5px;color:var(--ink-3)">{{ $foods->count() }} món</span>
    </div>

    @if($foods->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-hamburger"></i></div>
        <p style="font-size:14px;color:var(--ink-3);font-weight:500;margin-bottom:12px">Chưa có món ăn nào</p>
        <a href="/admin/foods/create" class="btn-add" style="display:inline-flex">
            <i class="fas fa-plus" style="font-size:11px"></i> Thêm món đầu tiên
        </a>
    </div>
    @else
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên món ăn</th>
                    <th>Giá bán</th>
                    <th>Voucher giá</th>
                    <th style="text-align:center">Trạng thái</th>
                    <th style="text-align:right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($foods as $food)
                @php
                    $hasVoucher = !is_null($food->voucher_price);
                    $isHot      = $hasVoucher && $food->voucher_price > $food->price;
                    $isSale     = $hasVoucher && $food->voucher_price < $food->price;
                @endphp
                <tr>
                    <td>
                        @if($food->image)
                            <img src="{{ $food->image }}" class="food-img" alt="{{ $food->name }}"
                                 onerror="this.outerHTML='<div class=\'food-img-placeholder\'><i class=\'fas fa-image\'></i></div>'">
                        @else
                            <div class="food-img-placeholder"><i class="fas fa-image"></i></div>
                        @endif
                    </td>
                    <td>
                        <div class="food-name">{{ $food->name }}</div>
                        <div class="food-id">ID #{{ $food->id }}</div>
                    </td>
                    <td>
                        <span class="food-price">{{ number_format($food->price) }}<span class="food-price-unit">đ</span></span>
                    </td>

                    {{-- ===== CỘT VOUCHER (thêm mới) ===== --}}
                    <td style="min-width:210px">
                        {{-- Badge + giá hiện tại --}}
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                            @if($isHot)
                                <span class="voucher-badge badge-hot"><i class="fas fa-fire" style="font-size:10px"></i> HOT</span>
                                <span class="voucher-price-val hot">{{ number_format($food->voucher_price) }}đ</span>
                            @elseif($isSale)
                                <span class="voucher-badge badge-sale"><i class="fas fa-percent" style="font-size:10px"></i> ƯU ĐÃI</span>
                                <span class="voucher-price-val sale">{{ number_format($food->voucher_price) }}đ</span>
                            @else
                                <span class="voucher-badge badge-none"><i class="fas fa-minus" style="font-size:10px"></i> Chưa có</span>
                            @endif
                        </div>

                        {{-- Gợi ý --}}
                        @if($isHot)
                            <div class="voucher-hint hint-hot">Giá cao hơn gốc → hiển thị HOT</div>
                        @elseif($isSale)
                            <div class="voucher-hint hint-sale">Giá thấp hơn gốc → hiển thị Ưu đãi</div>
                        @else
                            <div class="voucher-hint">Nhập giá voucher để áp dụng</div>
                        @endif

                        {{-- Form nhập / cập nhật / xóa --}}
                        <form action="/admin/foods/voucher/{{ $food->id }}" method="POST" class="voucher-form-wrap">
                            @csrf @method('PATCH')
                            <input
                                type="number"
                                name="voucher_price"
                                class="voucher-input"
                                placeholder="Nhập giá mới..."
                                value="{{ $food->voucher_price ?? '' }}"
                                min="0"
                                step="1000"
                            >
                            <button type="submit" class="btn-voucher-save">
                                <i class="fas fa-check" style="font-size:10px;margin-right:3px"></i>Lưu
                            </button>
                            @if($hasVoucher)
                            <button type="submit" name="voucher_price" value="" class="btn-voucher-clear" title="Xóa voucher">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </form>
                    </td>
                    {{-- ===== END VOUCHER ===== --}}

                    <td style="text-align:center">
                        <form action="/admin/foods/toggle-status/{{ $food->id }}" method="POST" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="toggle-btn {{ $food->is_active ? 'toggle-on' : 'toggle-off' }}">
                                <i class="fas {{ $food->is_active ? 'fa-eye' : 'fa-eye-slash' }}" style="font-size:10px"></i>
                                {{ $food->is_active ? 'Đang hiện' : 'Đã ẩn' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                            <a href="/admin/foods/edit/{{ $food->id }}" class="action-btn action-edit" title="Chỉnh sửa">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="/admin/foods/delete/{{ $food->id }}"
                               onclick="return confirm('Bạn có chắc muốn xóa món {{ $food->name }}?')"
                               class="action-btn action-delete" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection