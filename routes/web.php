<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Food;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\PointHistory;
use App\Models\Message;
use App\Http\Controllers\Admin\FoodController;
use App\Http\Controllers\Admin\OrderController;

/*
|--------------------------------------------------------------------------
| 1. TRANG CHỦ & NGƯỜI DÙNG (FRONTEND)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $foods = Food::where('is_active', 1)->latest()->get();
    return view('home', compact('foods'));
});

Route::post('/api/chat', function (Request $request) {
    $history = $request->input('history', []);
    $message = strtolower(end($history)['content'] ?? '');

    // Lấy danh sách món ăn từ DB
    $foods = Food::where('is_active', 1)->latest()->get();
    $menuText = $foods->isEmpty()
        ? 'Hiện chưa có món ăn nào.'
        : $foods->map(fn($f) => "- {$f->name}: " . number_format($f->price) . "đ")->join("\n");

    $reply = match(true) {
        str_contains($message, 'menu') || str_contains($message, 'món') || str_contains($message, 'hôm nay')
            => "🍔 Menu hôm nay:\n{$menuText}\n\nBạn muốn đặt món nào?",

        str_contains($message, 'khuyến mãi') || str_contains($message, 'giảm giá') || str_contains($message, 'ưu đãi')
            => "🎁 Khuyến mãi hôm nay:\n- Giảm 20% đơn hàng đầu tiên\n- Mua 2 tặng 1 với Burger\n- Miễn phí giao hàng đơn từ 100.000đ",

        str_contains($message, 'đơn hàng') || str_contains($message, 'theo dõi') || str_contains($message, 'giao hàng')
            => "🚀 Bạn có thể theo dõi đơn hàng tại mục **Đơn hàng** trên menu.\nThời gian giao hàng trung bình 25-30 phút.",

        str_contains($message, 'thanh toán') || str_contains($message, 'trả tiền')
            => "💳 FoodShop hỗ trợ 2 hình thức:\n- Thanh toán khi nhận hàng (COD)\n- Chuyển khoản QR Code",

        str_contains($message, 'liên hệ') || str_contains($message, 'hỗ trợ') || str_contains($message, 'hotline')
            => "📞 Liên hệ hỗ trợ:\n- Hotline: 1800 1234 (miễn phí)\n- Email: support@foodshop.vn\n- Giờ làm việc: 8h - 22h",

        str_contains($message, 'giờ') || str_contains($message, 'mở cửa')
            => "🕐 FoodShop mở cửa từ **8:00 - 22:00** tất cả các ngày trong tuần.",

        str_contains($message, 'xin chào') || str_contains($message, 'hello') || str_contains($message, 'hi') || str_contains($message, 'chào')
            => "👋 Xin chào! Tôi là FoodBot, trợ lý của FoodShop.\nTôi có thể giúp bạn:\n- Xem menu & đặt món\n- Theo dõi đơn hàng\n- Thông tin khuyến mãi\n- Hỗ trợ thanh toán",

        default
            => "Xin lỗi, tôi chưa hiểu câu hỏi của bạn 😅\nBạn có thể hỏi về:\n- 🍔 Menu món ăn\n- 🎁 Khuyến mãi\n- 🚀 Theo dõi đơn hàng\n- 💳 Thanh toán\n- 📞 Liên hệ hỗ trợ",
    };

    return response()->json(['reply' => $reply]);
});

/*
|--------------------------------------------------------------------------
| 2. HỆ THỐNG XÁC THỰC (AUTH)
|--------------------------------------------------------------------------
*/
Route::get('/auth', function () {
    return view('auth');
});

Route::post('/register', function (Request $request) {
    $request->validate([
        'name'     => 'required',
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:6'
    ]);
    User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);
    return back()->with('success', 'Đăng ký thành công!');
});

Route::post('/login', function (Request $request) {
    $request->validate(['email' => 'required|email', 'password' => 'required']);
    $user = User::where('email', $request->email)->first();
    if ($user && Hash::check($request->password, $user->password)) {
        session()->put('user', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
        return redirect('/')->with('success', 'Đăng nhập thành công!');
    }
    return back()->with('error', 'Sai tài khoản hoặc mật khẩu!');
});

Route::get('/logout', function () {
    session()->forget('user');
    return redirect('/')->with('success', 'Đã đăng xuất!');
});

/*
|--------------------------------------------------------------------------
| 2.5. TRANG HỒ SƠ NGƯỜI DÙNG (PROFILE)
|--------------------------------------------------------------------------
*/
Route::get('/profile', function () {
    if (!session('user')) return redirect('/auth')->with('error', 'Bạn cần đăng nhập!');

    $user    = User::find(session('user')['id']);
    $orders  = Order::where('user_id', $user->id)->latest()->get();

    $orders->each(function ($order) {
        $order->items = is_string($order->items)
            ? json_decode($order->items, true) ?? []
            : ($order->items ?? []);
    });

    $totalOrders = $orders->count();
    $totalSpent  = $orders->where('status', '!=', 'Đã hủy')->sum('total');

    $addresses = Address::where('user_id', $user->id)
        ->orderBy('is_default', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

    $pointHistory = PointHistory::where('user_id', $user->id)
        ->latest()
        ->get();

    return view('profile', compact('user', 'orders', 'totalOrders', 'totalSpent', 'addresses', 'pointHistory'));
});

Route::post('/profile/update', function (Request $request) {
    if (!session('user')) return redirect('/auth');

    $user = User::find(session('user')['id']);
    $user->update($request->only(['name', 'phone', 'birthday', 'gender']));

    session()->put('user', [
        'id'    => $user->id,
        'name'  => $user->name,
        'email' => $user->email,
    ]);

    return back()->with('success', 'Cập nhật thông tin thành công!');
});

Route::post('/profile/password', function (Request $request) {
    if (!session('user')) return redirect('/auth');

    $request->validate([
        'current_password' => 'required',
        'new_password'     => 'required|min:6|confirmed',
    ]);

    $user = User::find(session('user')['id']);

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng!']);
    }

    $user->update(['password' => Hash::make($request->new_password)]);
    return back()->with('success', 'Đổi mật khẩu thành công!');
});

Route::delete('/profile/delete', function () {
    if (!session('user')) return redirect('/auth');

    $user = User::find(session('user')['id']);
    session()->forget('user');
    $user->delete();

    return redirect('/')->with('success', 'Tài khoản đã được xóa!');
});

/*
|--------------------------------------------------------------------------
| 2.6. QUẢN LÝ ĐỊA CHỈ GIAO HÀNG
|--------------------------------------------------------------------------
*/
Route::post('/address/store', function (Request $request) {
    if (!session('user')) return redirect('/auth');

    $request->validate([
        'receiver_name' => 'required|string|max:100',
        'phone'         => 'required|string|max:20',
        'address'       => 'required|string|max:255',
    ], [
        'receiver_name.required' => 'Vui lòng nhập tên người nhận.',
        'phone.required'         => 'Vui lòng nhập số điện thoại.',
        'address.required'       => 'Vui lòng nhập địa chỉ.',
    ]);

    $userId = session('user')['id'];

    if ($request->boolean('is_default')) {
        Address::where('user_id', $userId)->update(['is_default' => false]);
    }

    $hasAny = Address::where('user_id', $userId)->exists();

    Address::create([
        'user_id'       => $userId,
        'receiver_name' => $request->receiver_name,
        'phone'         => $request->phone,
        'address'       => $request->address,
        'is_default'    => !$hasAny || $request->boolean('is_default'),
    ]);

    return redirect('/profile?tab=address')->with('address_success', 'Đã thêm địa chỉ mới!');
});

Route::get('/address/{id}/edit', function ($id) {
    if (!session('user')) return response()->json(['error' => 'Unauthorized'], 401);

    $address = Address::where('id', $id)
        ->where('user_id', session('user')['id'])
        ->firstOrFail();

    return response()->json($address);
});

Route::post('/address/{id}/update', function (Request $request, $id) {
    if (!session('user')) return redirect('/auth');

    $request->validate([
        'receiver_name' => 'required|string|max:100',
        'phone'         => 'required|string|max:20',
        'address'       => 'required|string|max:255',
    ], [
        'receiver_name.required' => 'Vui lòng nhập tên người nhận.',
        'phone.required'         => 'Vui lòng nhập số điện thoại.',
        'address.required'       => 'Vui lòng nhập địa chỉ.',
    ]);

    $userId = session('user')['id'];

    if ($request->boolean('is_default')) {
        Address::where('user_id', $userId)->update(['is_default' => false]);
    }

    Address::where('id', $id)
        ->where('user_id', $userId)
        ->update([
            'receiver_name' => $request->receiver_name,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'is_default'    => $request->boolean('is_default'),
        ]);

    return redirect('/profile?tab=address')->with('address_success', 'Đã cập nhật địa chỉ!');
});

Route::post('/address/{id}/default', function ($id) {
    if (!session('user')) return redirect('/auth');

    $userId = session('user')['id'];
    Address::where('user_id', $userId)->update(['is_default' => false]);
    Address::where('id', $id)->where('user_id', $userId)->update(['is_default' => true]);

    return redirect('/profile?tab=address')->with('address_success', 'Đã đặt địa chỉ mặc định!');
});

Route::delete('/address/{id}/delete', function ($id) {
    if (!session('user')) return redirect('/auth');

    $userId  = session('user')['id'];
    $address = Address::where('id', $id)->where('user_id', $userId)->firstOrFail();
    $wasDefault = $address->is_default;
    $address->delete();

    if ($wasDefault) {
        $next = Address::where('user_id', $userId)->first();
        if ($next) $next->update(['is_default' => true]);
    }

    return redirect('/profile?tab=address')->with('address_success', 'Đã xóa địa chỉ!');
});

/*
|--------------------------------------------------------------------------
| 3. GIỎ HÀNG & THANH TOÁN (USER)
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->group(function () {
    Route::post('/add/{id}', function ($id) {
        $food = Food::findOrFail($id);
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = ["name" => $food->name, "price" => $food->price, "image" => $food->image, "quantity" => 1];
        }
        session()->put('cart', $cart);
        return back()->with('success', 'Đã thêm vào giỏ hàng!');
    });

    Route::get('/', function () {
        $cart = session()->get('cart', []);
        return view('cart', compact('cart'));
    });

    Route::post('/update/{id}', function (Request $request, $id) {
        $cart = session()->get('cart', []);
        if (!isset($cart[$id])) return back();
        if ($request->type == 'increase') $cart[$id]['quantity']++;
        if ($request->type == 'decrease') {
            $cart[$id]['quantity']--;
            if ($cart[$id]['quantity'] <= 0) unset($cart[$id]);
        }
        session()->put('cart', $cart);
        return back();
    });

    Route::post('/remove/{id}', function ($id) {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) { unset($cart[$id]); session()->put('cart', $cart); }
        return back()->with('success', 'Đã xóa món!');
    });
});

Route::post('/checkout', function (Request $request) {
    if (!session('user')) return redirect('/auth')->with('error', 'Bạn cần đăng nhập!');
    $cart = session()->get('cart', []);
    if (empty($cart)) return back()->with('error', 'Giỏ hàng trống!');

    $total = 0;
    foreach ($cart as $item) { $total += $item['price'] * $item['quantity']; }

    Order::create([
        'user_id'        => session('user')['id'],
        'items'          => json_encode($cart),
        'total'          => $total,
        'status'         => 'Chờ xác nhận',
        'payment_method' => $request->payment_method,
    ]);

    session()->forget('cart');
    return redirect('/orders')->with('success', 'Đặt hàng thành công!');
});

Route::get('/orders', function () {
    if (!session('user')) return redirect('/auth');
    $orders = Order::where('user_id', session('user')['id'])->latest()->get();
    return view('orders', compact('orders'));
});

/*
|--------------------------------------------------------------------------
| 3.5. CHAT USER — nhắn tin với admin
|--------------------------------------------------------------------------
*/
Route::get('/chat', function () {
    if (!session('user')) return redirect('/auth')->with('error', 'Bạn cần đăng nhập!');
    $userId   = session('user')['id'];
    $messages = Message::where('user_id', $userId)->orderBy('created_at')->get();
    // Đánh dấu tin admin đã đọc bởi user
    Message::where('user_id', $userId)->where('sender', 'admin')->update(['is_read' => true]);
    return view('chat', compact('messages'));
});

Route::post('/chat/send', function (Request $request) {
    if (!session('user')) return response()->json(['error' => 'Unauthorized'], 401);
    $request->validate(['content' => 'required|string|max:1000']);
    $msg = Message::create([
        'user_id' => session('user')['id'],
        'content' => $request->content,
        'sender'  => 'user',
        'is_read' => false,
    ]);
    return response()->json(['message' => $msg->load('user')]);
});

// Polling: lấy tin mới hơn id X
Route::get('/chat/poll', function (Request $request) {
    if (!session('user')) return response()->json(['error' => 'Unauthorized'], 401);
    $userId  = session('user')['id'];
    $lastId  = $request->query('last_id', 0);
    $msgs    = Message::where('user_id', $userId)
                      ->where('id', '>', $lastId)
                      ->orderBy('created_at')
                      ->get();
    // Đánh dấu tin admin đã đọc
    Message::where('user_id', $userId)
           ->where('sender', 'admin')
           ->where('is_read', false)
           ->update(['is_read' => true]);
    return response()->json(['messages' => $msgs]);
});

/*
|--------------------------------------------------------------------------
| 4. HỆ THỐNG QUẢN TRỊ (ADMIN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    Route::get('/', fn() => redirect('/admin/dashboard'));

    Route::get('/dashboard', function () {
        $now       = now();
        $thisMonth = $now->month;
        $thisYear  = $now->year;
        $lastMonth = $now->copy()->subMonth();

        $totalRevenue = Order::where('status', '!=', 'Đã hủy')
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('total');
        $lastMonthRevenue = Order::where('status', '!=', 'Đã hủy')
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('total');
        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;
        $revenueDiff = $totalRevenue - $lastMonthRevenue;

        $totalOrders = Order::whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->count();
        $lastMonthOrders = Order::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();
        $ordersGrowth = $lastMonthOrders > 0
            ? round((($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1) : 0;
        $todayOrders = Order::whereDate('created_at', today())->count();

        $totalUsers = User::count();
        $lastMonthUsers = User::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();
        $usersGrowth = $lastMonthUsers > 0
            ? round((($totalUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1) : 0;
        $newUsers = User::where('created_at', '>=', $now->copy()->startOfWeek())->count();

        $totalFoods  = Food::where('is_active', 1)->count();
        $hiddenFoods = Food::where('is_active', 0)->count();

        $labels7 = $revenue7 = $orders7 = [];
        foreach (range(6, 0) as $i) {
            $day = $now->copy()->subDays($i);
            $labels7[]  = $day->locale('vi')->isoFormat('dd');
            $revenue7[] = (int) Order::whereDate('created_at', $day->toDateString())->where('status', '!=', 'Đã hủy')->sum('total');
            $orders7[]  = Order::whereDate('created_at', $day->toDateString())->count();
        }

        $labels30 = $revenue30 = $orders30 = [];
        foreach (range(29, 0) as $i) {
            $day = $now->copy()->subDays($i);
            $labels30[]  = $day->format('d');
            $revenue30[] = (int) Order::whereDate('created_at', $day->toDateString())->where('status', '!=', 'Đã hủy')->sum('total');
            $orders30[]  = Order::whereDate('created_at', $day->toDateString())->count();
        }

        $rawStatus = Order::whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)
            ->select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status');
        $statusMap = [
            'Hoàn thành'   => $rawStatus['Hoàn thành']   ?? 0,
            'Đang giao'    => $rawStatus['Đang giao']     ?? 0,
            'Chờ xác nhận' => $rawStatus['Chờ xác nhận'] ?? 0,
            'Đã hủy'       => $rawStatus['Đã hủy']       ?? 0,
        ];

        $allOrders = Order::where('status', '!=', 'Đã hủy')
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->get(['items']);
        $foodSales = [];
        foreach ($allOrders as $order) {
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            if (!is_array($items)) continue;
            foreach ($items as $item) {
                $name = $item['name'] ?? 'Không rõ';
                $foodSales[$name] = ($foodSales[$name] ?? 0) + (int)($item['quantity'] ?? 1);
            }
        }
        arsort($foodSales);
        $topFoodNames = array_keys(array_slice($foodSales, 0, 5));
        $topFoodsDb   = Food::whereIn('name', $topFoodNames)->get()->keyBy('name');
        $topFoodList  = collect($topFoodNames)->map(fn($name) => [
            'food' => $topFoodsDb->get($name), 'name' => $name, 'sold' => $foodSales[$name],
        ]);

        $totalSold = array_sum($foodSales);
        $categoryData = [
            ['label' => '🍔 Burger & Fast food', 'color' => '#E8501A', 'sold' => 0],
            ['label' => '🍕 Pizza',               'color' => '#1D6FBE', 'sold' => 0],
            ['label' => '🍜 Món Á',               'color' => '#16A34A', 'sold' => 0],
            ['label' => '🥗 Món khác',            'color' => '#B45309', 'sold' => 0],
        ];
        foreach ($foodSales as $name => $qty) {
            $lower = mb_strtolower($name);
            if (str_contains($lower, 'burger') || str_contains($lower, 'bánh mì') || str_contains($lower, 'sandwich'))
                $categoryData[0]['sold'] += $qty;
            elseif (str_contains($lower, 'pizza'))
                $categoryData[1]['sold'] += $qty;
            elseif (str_contains($lower, 'phở') || str_contains($lower, 'mì') || str_contains($lower, 'cơm') || str_contains($lower, 'bún') || str_contains($lower, 'ramen'))
                $categoryData[2]['sold'] += $qty;
            else
                $categoryData[3]['sold'] += $qty;
        }
        foreach ($categoryData as &$cat) {
            $cat['pct'] = $totalSold > 0 ? round($cat['sold'] / $totalSold * 100) : 0;
        }

        $recentOrders = Order::with('user')->latest()->take(8)->get();
        $pendingCount = Order::where('status', 'Chờ xác nhận')->count();

        return view('admin.index', compact(
            'totalRevenue', 'lastMonthRevenue', 'revenueGrowth', 'revenueDiff',
            'totalOrders', 'lastMonthOrders', 'ordersGrowth', 'todayOrders',
            'totalUsers', 'usersGrowth', 'newUsers',
            'totalFoods', 'hiddenFoods',
            'labels7', 'revenue7', 'orders7',
            'labels30', 'revenue30', 'orders30',
            'statusMap', 'topFoodList', 'categoryData', 'recentOrders',
            'pendingCount'
        ));
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN — CHAT (nhắn tin với user)
    |--------------------------------------------------------------------------
    */
    Route::get('/chat', function () {
        $users = User::whereHas('messages')->withCount([
            'messages as unread_count' => fn($q) => $q->where('sender', 'user')->where('is_read', false)
        ])->get();
        return view('admin.chat', compact('users'));
    });

    Route::get('/chat/unread-count', function () {
        $count = Message::where('sender', 'user')->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    });

    Route::get('/chat/{userId}', function ($userId) {
        $user     = User::findOrFail($userId);
        $messages = Message::where('user_id', $userId)->orderBy('created_at')->get();
        // Đánh dấu tin user đã đọc bởi admin
        Message::where('user_id', $userId)->where('sender', 'user')->update(['is_read' => true]);
        $users = User::whereHas('messages')->withCount([
            'messages as unread_count' => fn($q) => $q->where('sender', 'user')->where('is_read', false)
        ])->get();
        return view('admin.chat', compact('user', 'users', 'messages'));
    });

    Route::post('/chat/{userId}/send', function (Request $request, $userId) {
        $request->validate(['content' => 'required|string|max:1000']);
        $msg = Message::create([
            'user_id' => $userId,
            'content' => $request->content,
            'sender'  => 'admin',
            'is_read' => false,
        ]);
        return response()->json(['message' => $msg]);
    });

    // Polling admin: lấy tin mới trong conversation
    Route::get('/chat/{userId}/poll', function (Request $request, $userId) {
        $lastId = $request->query('last_id', 0);
        $msgs   = Message::where('user_id', $userId)
                         ->where('id', '>', $lastId)
                         ->orderBy('created_at')
                         ->get();
        Message::where('user_id', $userId)->where('sender', 'user')->where('is_read', false)
               ->update(['is_read' => true]);
        // Tổng unread mỗi user để update sidebar
        $unreadMap = Message::where('sender', 'user')->where('is_read', false)
                            ->selectRaw('user_id, count(*) as cnt')
                            ->groupBy('user_id')
                            ->pluck('cnt', 'user_id');
        return response()->json(['messages' => $msgs, 'unread_map' => $unreadMap]);
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN — ORDERS & FOODS
    |--------------------------------------------------------------------------
    */
    Route::get('/orders',                      [OrderController::class, 'index']);
    Route::patch('/orders/update-status/{id}', [OrderController::class, 'updateStatus']);

    Route::get('/foods',                       [FoodController::class, 'index']);
    Route::get('/foods/create',                [FoodController::class, 'create']);
    Route::post('/foods/store',                [FoodController::class, 'store']);
    Route::get('/foods/edit/{id}',             [FoodController::class, 'edit']);
    Route::post('/foods/update/{id}',          [FoodController::class, 'update']);
    Route::get('/foods/delete/{id}',           [FoodController::class, 'destroy']);
    Route::patch('/foods/toggle-status/{id}',  [FoodController::class, 'toggleStatus']);
    Route::patch('/foods/voucher/{id}',        [FoodController::class, 'updateVoucher']);
});