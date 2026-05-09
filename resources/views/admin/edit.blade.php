@extends('layouts.admin')

@section('title', 'Chỉnh sửa món ăn')
@section('page-title', 'Chỉnh sửa món ăn')
@section('page-sub', 'Cập nhật thông tin món ăn')

@section('head')
<style>
    .form-wrap { max-width: 640px; }
    .form-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 32px;
    }
    .breadcrumb {
        display: flex; align-items: center; gap: 8px;
        font-size: 12.5px; color: var(--ink-3);
        margin-bottom: 20px;
    }
    .breadcrumb a { color: var(--ink-3); text-decoration: none; transition: color 0.15s; }
    .breadcrumb a:hover { color: var(--brand); }
    .breadcrumb i { font-size: 10px; }

    .field-label {
        display: block;
        font-size: 11px; font-weight: 700;
        color: var(--ink-3); text-transform: uppercase;
        letter-spacing: 0.8px; margin-bottom: 7px;
    }

    .input-field {
        width: 100%;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13.5px;
        color: var(--ink);
        outline: none;
        transition: border-color 0.18s, box-shadow 0.18s;
        background: var(--surface);
        font-family: 'DM Sans', sans-serif;
    }
    .input-field:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(232,80,26,0.1);
        background: var(--card);
    }
    .input-wrap { position: relative; }
    .input-suffix {
        position: absolute; right: 12px; top: 50%;
        transform: translateY(-50%);
        font-size: 13px; color: var(--ink-3); font-weight: 500;
    }

    .form-field { margin-bottom: 20px; }

    .current-img {
        width: 100px; height: 100px;
        border-radius: 12px; object-fit: cover;
        border: 1px solid var(--border);
        margin-bottom: 12px;
        display: block;
    }
    .img-section {
        border: 1.5px dashed var(--border);
        border-radius: 12px;
        padding: 20px;
    }
    .img-section-label {
        font-size: 12px; color: var(--ink-3); margin-bottom: 12px;
        font-weight: 500;
    }
    .file-input-wrap {
        display: flex; align-items: center; gap: 12px;
    }
    .file-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--surface); border: 1.5px solid var(--border);
        color: var(--ink-2); font-size: 12.5px; font-weight: 600;
        padding: 8px 14px; border-radius: 9px;
        cursor: pointer; transition: all 0.18s;
        font-family: 'DM Sans', sans-serif;
    }
    .file-btn:hover { border-color: var(--brand); color: var(--brand); background: var(--brand-lt); }
    .file-name { font-size: 12px; color: var(--ink-3); }

    .btn-submit {
        flex: 1;
        background: var(--ink); color: white;
        font-size: 13.5px; font-weight: 700;
        padding: 13px; border-radius: 11px;
        border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        display: flex; align-items: center; justify-content: center; gap: 7px;
        transition: background 0.18s;
    }
    .btn-submit:hover { background: var(--brand); }

    .btn-cancel {
        padding: 13px 24px; border-radius: 11px;
        border: 1.5px solid var(--border);
        background: transparent;
        color: var(--ink-3); font-size: 13.5px; font-weight: 500;
        text-decoration: none; transition: all 0.18s;
    }
    .btn-cancel:hover { background: var(--surface); color: var(--ink); }

    .error-box {
        background: var(--red-lt); border: 1px solid #FECACA;
        color: #DC2626; padding: 12px 16px;
        border-radius: 10px; font-size: 13px;
        margin-bottom: 20px;
    }
    .error-box div { display: flex; align-items: center; gap: 7px; margin-bottom: 4px; }
    .error-box div:last-child { margin-bottom: 0; }
</style>
@endsection

@section('content')
<div class="form-wrap">

    <div class="breadcrumb">
        <a href="/admin/foods">Quản lý món</a>
        <i class="fas fa-chevron-right"></i>
        <span style="color:var(--ink);font-weight:500">Chỉnh sửa: {{ $food->name }}</span>
    </div>

    <div class="form-card">

        @if($errors->any())
        <div class="error-box">
            @foreach($errors->all() as $error)
            <div><i class="fas fa-exclamation-circle" style="font-size:11px"></i> {{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form action="/admin/foods/update/{{ $food->id }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-field">
                <label class="field-label">Tên món ăn</label>
                <input name="name" type="text" class="input-field"
                    value="{{ old('name', $food->name) }}" required>
            </div>

            <div class="form-field">
                <label class="field-label">Mô tả</label>
                <textarea name="description" rows="3" class="input-field" style="resize:none">{{ old('description', $food->description) }}</textarea>
            </div>

            <div class="form-field">
                <label class="field-label">Giá bán (VNĐ)</label>
                <div class="input-wrap">
                    <input name="price" type="number" min="0" class="input-field"
                        style="padding-right:36px"
                        value="{{ old('price', $food->price) }}" required>
                    <span class="input-suffix">đ</span>
                </div>
            </div>

            <div class="form-field">
                <label class="field-label">Hình ảnh</label>
                <div class="img-section">
                    <p class="img-section-label">Ảnh hiện tại:</p>
                    <img id="preview" src="{{ $food->image }}" class="current-img" alt="{{ $food->name }}">
                    <p class="img-section-label">Chọn ảnh mới (để trống nếu không đổi):</p>
                    <div class="file-input-wrap">
                        <label class="file-btn" for="imageInput">
                            <i class="fas fa-upload" style="font-size:11px"></i> Chọn file
                        </label>
                        <span class="file-name" id="fileName">Chưa chọn file</span>
                        <input type="file" id="imageInput" name="image" accept="image/*"
                            style="display:none" onchange="previewImage(event)">
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save" style="font-size:11px"></i> Lưu thay đổi
                </button>
                <a href="/admin/foods" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    document.getElementById('preview').src = URL.createObjectURL(file);
    document.getElementById('fileName').textContent = file.name;
}
</script>
@endsection