<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    // ─── Danh sách ───────────────────────────────────────────────────────────
    public function index()
    {
        $foods = Food::latest()->get();
        return view('admin.foods', compact('foods'));
    }

    // ─── Form tạo mới ────────────────────────────────────────────────────────
    public function create()
    {
        return view('admin.create');
    }

    // ─── Lưu mới ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('foods', 'public');
            $data['image'] = '/storage/' . $data['image'];
        }

        Food::create($data);

        return redirect('/admin/foods')->with('success', 'Đã thêm món ăn thành công!');
    }

    // ─── Form chỉnh sửa ──────────────────────────────────────────────────────
    public function edit($id)
    {
        $food = Food::findOrFail($id);
        return view('admin.edit', compact('food'));
    }

    // ─── Cập nhật ────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $food = Food::findOrFail($id);
        $data = $request->only(['name', 'price', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('foods', 'public');
            $data['image'] = '/storage/' . $data['image'];
        }

        $food->update($data);

        return redirect('/admin/foods')->with('success', 'Đã cập nhật món ăn!');
    }

    // ─── Xóa ─────────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        Food::findOrFail($id)->delete();
        return redirect('/admin/foods')->with('success', 'Đã xóa món ăn!');
    }

    // ─── Bật / tắt hiển thị ──────────────────────────────────────────────────
    public function toggleStatus($id)
    {
        $food = Food::findOrFail($id);
        $food->update(['is_active' => !$food->is_active]);
        return back()->with('success', 'Đã cập nhật trạng thái!');
    }

    // ─── CẬP NHẬT VOUCHER GIÁ ────────────────────────────────────────────────
    public function updateVoucher(Request $request, $id)
    {
        $food     = Food::findOrFail($id);
        $rawValue = $request->input('voucher_price');

        // Chuỗi rỗng hoặc null → xóa voucher
        if ($rawValue === '' || $rawValue === null) {
            $food->update(['voucher_price' => null]);
            return back()->with('success', 'Đã xóa voucher cho món "' . $food->name . '".');
        }

        // Validate số dương
        $request->validate([
            'voucher_price' => 'required|numeric|min:0',
        ], [
            'voucher_price.required' => 'Vui lòng nhập giá voucher.',
            'voucher_price.numeric'  => 'Giá voucher phải là số.',
            'voucher_price.min'      => 'Giá voucher không được âm.',
        ]);

        $voucherPrice = (int) $request->input('voucher_price');
        $food->update(['voucher_price' => $voucherPrice]);

        if ($voucherPrice > $food->price) {
            $msg = 'Đã đặt giá HOT ' . number_format($voucherPrice) . 'đ cho món "' . $food->name . '".';
        } elseif ($voucherPrice < $food->price) {
            $pct = round((1 - $voucherPrice / $food->price) * 100);
            $msg = 'Đã đặt ưu đãi -' . $pct . '% → ' . number_format($voucherPrice) . 'đ cho món "' . $food->name . '".';
        } else {
            $msg = 'Giá voucher bằng giá gốc — không có hiệu lực đặc biệt.';
        }

        return back()->with('success', $msg);
    }
}