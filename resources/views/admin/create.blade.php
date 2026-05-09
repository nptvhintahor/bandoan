@extends('layouts.admin')

@section('title', 'Thêm món mới')
@section('page-title', 'Thêm món mới')
@section('page-sub', 'Điền thông tin để thêm món ăn vào thực đơn')

@section('head')
<style>
    .form-wrap {
        max-width: 640px;
    }
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
    .field-label span { color: #DC2626; }

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

    .drop-zone {
        border: 2px dashed var(--border);
        border-radius: 12px;
        padding: 28px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.18s;
    }
    .drop-zone:hover, .drop-zone.active {
        border-color: var(--brand);
        background: var(--brand-lt);
    }
    .drop-icon {
        width: 48px; height: 48px;
        background: var(--surface); border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px;
        font-size: 20px; color: var(--ink-3);
    }

    .form-field { margin-bottom: 20px; }

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
        font-family: 'DM Sans', sans-serif;
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
        <span style="color:var(--ink);font-weight:500">Thêm món mới</span>
    </div>

    <div class="form-card">

        @if($errors->any())
        <div class="error-box">
            @foreach($errors->all() as $error)
            <div><i class="fas fa-exclamation-circle" style="font-size:11px"></i> {{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form action="/admin/foods/store" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-field">
                <label class="field-label">Tên món ăn <span>*</span></label>
                <input name="name" type="text" placeholder="VD: Phở bò đặc biệt"
                    class="input-field" value="{{ old('name') }}" required>
            </div>

            <div class="form-field">
                <label class="field-label">Mô tả</label>
                <textarea name="description" rows="3" placeholder="Mô tả ngắn về món ăn..."
                    class="input-field" style="resize:none">{{ old('description') }}</textarea>
            </div>

            <div class="form-field">
                <label class="field-label">Giá bán (VNĐ) <span>*</span></label>
                <div class="input-wrap">
                    <input name="price" type="number" min="0" placeholder="VD: 65000"
                        class="input-field" style="padding-right:36px" value="{{ old('price') }}" required>
                    <span class="input-suffix">đ</span>
                </div>
            </div>

            <div class="form-field">
                <label class="field-label">Hình ảnh <span>*</span></label>
                <div class="drop-zone" id="dropZone" onclick="document.getElementById('imageInput').click()">
                    <div id="previewWrap" style="display:none;margin-bottom:12px">
                        <img id="preview" style="width:120px;height:120px;object-fit:cover;border-radius:12px;margin:0 auto;display:block;box-shadow:0 4px 14px rgba(0,0,0,0.1)">
                    </div>
                    <div id="placeholder">
                        <div class="drop-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <p style="font-size:13.5px;font-weight:500;color:var(--ink-2)">Bấm để chọn ảnh</p>
                        <p style="font-size:12px;color:var(--ink-3);margin-top:4px">PNG, JPG, WEBP — tối đa 2MB</p>
                    </div>
                    <input type="file" id="imageInput" name="image" accept="image/*" class="hidden" style="display:none" onchange="previewImage(event)" required>
                </div>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-plus" style="font-size:11px"></i> Thêm món
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
    document.getElementById('previewWrap').style.display = 'block';
    document.getElementById('placeholder').style.display = 'none';
    document.getElementById('dropZone').classList.add('active');
}
</script>
@endsection