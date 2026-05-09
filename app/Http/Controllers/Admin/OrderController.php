<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Food;
use App\Models\User;
use App\Models\PointHistory;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng và thống kê
     */
    public function index()
    {
        $orders = Order::with('user')->latest()->get();
        $foods = Food::all();
        $pendingCount = $orders->where('status', 'Chờ xác nhận')->count();

        return view('admin.order-management', compact('orders', 'foods', 'pendingCount'));
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * Khi chuyển sang "Hoàn thành" → cộng 100 điểm cho user (chỉ 1 lần)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $order     = Order::findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        $order->status = $newStatus;
        $order->save();

        // Cộng điểm khi chuyển sang "Hoàn thành", chỉ cộng 1 lần
        if ($newStatus === 'Hoàn thành' && $oldStatus !== 'Hoàn thành') {
            $user = User::find($order->user_id);

            if ($user) {
                $user->increment('points', 100);

                PointHistory::create([
                    'user_id'     => $user->id,
                    'amount'      => 100,
                    'description' => 'Hoàn thành đơn hàng #' . $order->id,
                    'order_id'    => $order->id,
                ]);
            }
        }

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng thành công!');
    }
}