<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    // Lấy toàn bộ menu đang hoạt động
    $foods = Food::where('is_active', 1)->latest()->get();

    $menuText = $foods->isEmpty()
        ? 'Hiện chưa có món ăn nào.'
        : $foods->map(fn($f) => "- {$f->name}: " . number_format($f->price) . "đ")->join("\n");

    // ── Hàm helper: lọc món theo từ khoá trong tên ──────────────────────────
    $filterMenu = function (array $keywords) use ($foods): string {
        $filtered = $foods->filter(function ($f) use ($keywords) {
            $lower = mb_strtolower($f->name);
            foreach ($keywords as $kw) {
                if (str_contains($lower, $kw)) return true;
            }
            return false;
        });
        return $filtered->isEmpty()
            ? ''
            : $filtered->map(fn($f) => "- {$f->name}: " . number_format($f->price) . "đ")->join("\n");
    };

    // Từ khoá phân loại
    $kwFastFood = ['burger', 'gà', 'khoai', 'hot dog', 'nugget', 'sandwich', 'bánh mì', 'pizza', 'wrap'];
    $kwHealthy  = ['salad', 'healthy', 'rau', 'ức gà', 'hoa quả', 'trái cây', 'ngũ cốc', 'smoothie bowl', 'yến mạch'];
    $kwDrink    = ['nước', 'trà', 'cà phê', 'juice', 'sinh tố', 'soda', 'cola', 'latte', 'cappuccino', 'matcha', 'trà sữa', 'ép', 'đá'];
    $kwDessert  = ['bánh', 'kem', 'chè', 'pudding', 'tiramisu', 'donut', 'waffle', 'pancake', 'cupcake'];
    $kwRice     = ['cơm', 'cháo', 'xôi'];
    $kwNoodle   = ['phở', 'bún', 'mì', 'hủ tiếu', 'ramen', 'udon', 'spaghetti', 'pasta'];
    $kwVeg      = ['chay', 'thuần chay', 'vegan'];
    $kwSpicy    = ['cay', 'lẩu', 'ớt'];
    $kwSeafood  = ['tôm', 'cá', 'mực', 'hải sản', 'cua', 'bạch tuộc'];
    $kwSnack    = ['snack', 'ăn vặt', 'khoai tây', 'onion ring', 'mozzarella stick'];

    // ── Kịch bản gợi ý MỞ ĐẦU (người dùng hỏi gợi ý chung) ─────────────────
    $wantSuggest = str_contains($message, 'gợi ý')
        || str_contains($message, 'giới thiệu')
        || str_contains($message, 'tư vấn')
        || str_contains($message, 'recommend')
        || str_contains($message, 'muốn ăn gì')
        || str_contains($message, 'ăn gì ngon')
        || str_contains($message, 'uống gì ngon')
        || str_contains($message, 'chọn món')
        || str_contains($message, 'không biết ăn gì')
        || str_contains($message, 'không biết uống gì');

    // ── Kịch bản hỏi cụ thể loại món ────────────────────────────────────────
    $isFastFood = str_contains($message, 'đồ ăn nhanh')
        || str_contains($message, 'fast food')
        || str_contains($message, 'burger')
        || str_contains($message, 'pizza')
        || str_contains($message, 'gà rán');

    $isHealthy  = str_contains($message, 'healthy')
        || str_contains($message, 'tốt cho sức khoẻ')
        || str_contains($message, 'tốt cho sức khỏe')
        || str_contains($message, 'ít calo')
        || str_contains($message, 'eat clean')
        || str_contains($message, 'giảm cân')
        || str_contains($message, 'rau');

    $isDrink    = str_contains($message, 'đồ uống')
        || str_contains($message, 'nước uống')
        || str_contains($message, 'uống')
        || str_contains($message, 'trà')
        || str_contains($message, 'cà phê')
        || str_contains($message, 'sinh tố')
        || str_contains($message, 'juice')
        || str_contains($message, 'soda');

    $isDessert  = str_contains($message, 'tráng miệng')
        || str_contains($message, 'dessert')
        || str_contains($message, 'đồ ngọt')
        || str_contains($message, 'bánh ngọt')
        || str_contains($message, 'kem');

    $isRice     = str_contains($message, 'cơm')
        || str_contains($message, 'cháo')
        || str_contains($message, 'xôi');

    $isNoodle   = str_contains($message, 'phở')
        || str_contains($message, 'bún')
        || str_contains($message, 'mì')
        || str_contains($message, 'pasta')
        || str_contains($message, 'ramen');

    $isVeg      = str_contains($message, 'chay')
        || str_contains($message, 'vegan')
        || str_contains($message, 'thuần chay');

    $isSpicy    = str_contains($message, 'cay')
        || str_contains($message, 'lẩu')
        || str_contains($message, 'đồ cay');

    $isSeafood  = str_contains($message, 'hải sản')
        || str_contains($message, 'tôm')
        || str_contains($message, 'cá')
        || str_contains($message, 'mực');

    $isSnack    = str_contains($message, 'ăn vặt')
        || str_contains($message, 'snack')
        || str_contains($message, 'khai vị');

    $isCheap    = str_contains($message, 'rẻ')
        || str_contains($message, 'tiết kiệm')
        || str_contains($message, 'ít tiền')
        || str_contains($message, 'bình dân')
        || str_contains($message, 'dưới 50')
        || str_contains($message, 'dưới 100');

    $isExpensive = str_contains($message, 'cao cấp')
        || str_contains($message, 'sang')
        || str_contains($message, 'xịn')
        || str_contains($message, 'ngon nhất')
        || str_contains($message, 'đặc biệt');

    $isCombo    = str_contains($message, 'combo')
        || str_contains($message, 'set')
        || str_contains($message, 'phần ăn');

    $isHot      = str_contains($message, 'nóng')
        || str_contains($message, 'ấm')
        || str_contains($message, 'soup')
        || str_contains($message, 'canh');

    $isCold     = str_contains($message, 'lạnh')
        || str_contains($message, 'mát')
        || str_contains($message, 'đá')
        || str_contains($message, 'giải nhiệt');

    $isBreakfast = str_contains($message, 'sáng')
        || str_contains($message, 'bữa sáng')
        || str_contains($message, 'breakfast');

    $isLunch    = str_contains($message, 'trưa')
        || str_contains($message, 'bữa trưa')
        || str_contains($message, 'lunch');

    $isDinner   = str_contains($message, 'tối')
        || str_contains($message, 'bữa tối')
        || str_contains($message, 'dinner');

    // ── PRE-COMPUTE các reply cho từng kịch bản (tránh IIFE trong match) ────

    // 2. Đồ ăn nhanh
    $listFastFood = $filterMenu($kwFastFood);
    $replyFastFood = $listFastFood
        ? "🍔 Các món đồ ăn nhanh hôm nay:\n{$listFastFood}\n\nBạn muốn thêm vào giỏ hàng món nào không?"
        : "😔 Hôm nay chưa có đồ ăn nhanh trong menu. Bạn thử xem các món khác nhé!\n\nGõ **menu** để xem toàn bộ món.";

    // 3. Healthy / Eat clean
    $listHealthy = $filterMenu($kwHealthy);
    $replyHealthy = $listHealthy
        ? "🥗 Các món tốt cho sức khoẻ hôm nay:\n{$listHealthy}\n\nĂn healthy giúp bạn tràn đầy năng lượng cả ngày! 💪"
        : "😔 Hôm nay chưa có món healthy trong menu. Bạn có thể thử các món ít dầu mỡ khác nhé!";

    // 4. Đồ uống
    $listDrink = $filterMenu($kwDrink);
    $replyDrink = $listDrink
        ? "🧋 Đồ uống có trong menu hôm nay:\n{$listDrink}\n\nBạn muốn uống nóng hay lạnh? Tôi có thể gợi ý thêm!"
        : "😔 Hôm nay chưa có đồ uống trong menu. Bạn thử món ăn khác nhé!";

    // 5. Tráng miệng / Đồ ngọt
    $listDessert = $filterMenu($kwDessert);
    $replyDessert = $listDessert
        ? "🍰 Các món tráng miệng & đồ ngọt hôm nay:\n{$listDessert}\n\nNgọt ngào kết thúc bữa ăn hoàn hảo! 🍮"
        : "😔 Hôm nay chưa có món tráng miệng. Bạn quay lại sau nhé!";

    // 6. Cơm / Cháo / Xôi
    $listRice = $filterMenu($kwRice);
    $replyRice = $listRice
        ? "🍚 Các món cơm / cháo / xôi hôm nay:\n{$listRice}\n\nNo bụng, chắc dạ! 😄"
        : "😔 Hôm nay chưa có món cơm / cháo / xôi. Bạn thử phở hay bún không?";

    // 7. Phở / Bún / Mì / Pasta
    $listNoodle = $filterMenu($kwNoodle);
    $replyNoodle = $listNoodle
        ? "🍜 Các món phở / bún / mì hôm nay:\n{$listNoodle}\n\nĂn tô mì nóng hổi cho ấm bụng nào! 🔥"
        : "😔 Hôm nay chưa có món phở / bún / mì. Bạn thử cơm hay món khác nhé!";

    // 8. Món chay / Vegan
    $listVeg = $filterMenu($kwVeg);
    $replyVeg = $listVeg
        ? "🌿 Các món chay / thuần chay hôm nay:\n{$listVeg}\n\nĂn chay thanh tịnh, nhẹ nhàng cho cơ thể! 🙏"
        : "😔 Hôm nay chưa có món chay trong menu. Bạn thử các món healthy khác nhé!";

    // 9. Đồ cay / Lẩu
    $listSpicy = $filterMenu($kwSpicy);
    $replySpicy = $listSpicy
        ? "🌶️ Các món cay / lẩu hôm nay:\n{$listSpicy}\n\nCẩn thận không? Cay lắm đấy! 🔥😄"
        : "😔 Hôm nay chưa có món cay / lẩu. Bạn thử món khác nhé!";

    // 10. Hải sản
    $listSeafood = $filterMenu($kwSeafood);
    $replySeafood = $listSeafood
        ? "🦐 Các món hải sản hôm nay:\n{$listSeafood}\n\nTươi ngon từ biển cả! 🌊"
        : "😔 Hôm nay chưa có món hải sản. Bạn thử các món khác nhé!";

    // 11. Ăn vặt / Khai vị / Snack
    $listSnack = $filterMenu($kwSnack);
    $replySnack = $listSnack
        ? "🥪 Các món ăn vặt / khai vị hôm nay:\n{$listSnack}\n\nNhâm nhi cho vui miệng nào! 😋"
        : "😔 Hôm nay chưa có món ăn vặt. Bạn thử xem menu đầy đủ nhé!";

    // 12. Món rẻ / Tiết kiệm
    $filteredCheap = $foods->where('price', '<=', 80000)->sortBy('price');
    $replyCheap = $filteredCheap->isEmpty()
        ? "😔 Hiện tại chưa có món dưới 80.000đ. Bạn xem menu đầy đủ nhé!"
        : "💰 Các món tiết kiệm (dưới 80.000đ) hôm nay:\n"
            . $filteredCheap->map(fn($f) => "- {$f->name}: " . number_format($f->price) . "đ")->join("\n")
            . "\n\nVừa ngon vừa rẻ! 😄";

    // 13. Món cao cấp / Đặc biệt
    $filteredExpensive = $foods->sortByDesc('price')->take(5);
    $replyExpensive = $filteredExpensive->isEmpty()
        ? "😔 Chưa có món đặc biệt hôm nay."
        : "✨ Các món cao cấp & đặc biệt của FoodShop:\n"
            . $filteredExpensive->map(fn($f) => "- {$f->name}: " . number_format($f->price) . "đ")->join("\n")
            . "\n\nXứng đáng để bạn thưởng thức! 🌟";

    // 14. Combo / Set
    $listCombo = $filterMenu(['combo', 'set']);
    $replyCombo = $listCombo
        ? "🎁 Các combo / set ăn hôm nay:\n{$listCombo}\n\nGiá trị, no bụng và tiết kiệm hơn! 🤩"
        : "😔 Hôm nay chưa có combo trong menu. Bạn có thể tự ghép món yêu thích nhé!\n\nGõ **menu** để xem toàn bộ.";

    // 15. Đồ nóng / Ấm
    $listHot = $filterMenu(array_merge($kwNoodle, $kwRice, ['canh', 'soup', 'lẩu', 'nóng']));
    $replyHot = $listHot
        ? "🔥 Các món nóng hổi hôm nay:\n{$listHot}\n\nĂn nóng cho ấm người nhé! ☕"
        : "😔 Hôm nay chưa có nhiều món nóng. Bạn xem menu đầy đủ nhé!";

    // 16. Đồ lạnh / Mát / Giải nhiệt
    $listCold = $filterMenu(array_merge($kwDrink, $kwDessert, ['đá', 'lạnh', 'mát']));
    $replyCold = $listCold
        ? "🧊 Các món mát lạnh & giải nhiệt hôm nay:\n{$listCold}\n\nGiải nhiệt ngày hè nào! ❄️"
        : "😔 Hôm nay chưa có nhiều món lạnh. Bạn xem menu đầy đủ nhé!";

    // 17. Bữa sáng
    $listBreakfast = $filterMenu(['bánh mì', 'xôi', 'cháo', 'sữa', 'ngũ cốc', 'sandwich', 'pancake', 'waffle', 'trứng', 'yến mạch']);
    $replyBreakfast = $listBreakfast
        ? "🌅 Gợi ý bữa sáng hôm nay:\n{$listBreakfast}\n\nKhởi đầu ngày mới thật năng lượng! ☀️"
        : "😔 Hôm nay chưa có nhiều món phù hợp bữa sáng. Bạn xem menu nhé!";

    // 18. Bữa trưa
    $listLunch = $filterMenu(array_merge($kwRice, $kwNoodle, ['cơm', 'phở', 'bún', 'mì', 'gà', 'thịt', 'cá']));
    $replyLunch = $listLunch
        ? "🌞 Gợi ý bữa trưa hôm nay:\n{$listLunch}\n\nNo bụng chiến đấu buổi chiều thôi! 💪"
        : "😔 Hôm nay chưa có nhiều món phù hợp bữa trưa. Bạn xem menu nhé!";

    // 19. Bữa tối
    $listDinner = $filterMenu(array_merge($kwFastFood, $kwSeafood, $kwSpicy, ['pizza', 'lẩu', 'nướng', 'steak']));
    $replyDinner = $listDinner
        ? "🌙 Gợi ý bữa tối hôm nay:\n{$listDinner}\n\nBữa tối ngon miệng cùng gia đình & bạn bè! 🥂"
        : "😔 Hôm nay chưa có nhiều món phù hợp bữa tối. Bạn xem menu nhé!";

    // ── ROUTING CÁC KỊCH BẢN ─────────────────────────────────────────────────
    $reply = match(true) {

        // 1. Gợi ý mở đầu chung — hỏi lại sở thích
        $wantSuggest && !$isFastFood && !$isHealthy && !$isDrink && !$isDessert
            && !$isRice && !$isNoodle && !$isVeg && !$isSpicy && !$isSeafood
            && !$isSnack && !$isCheap && !$isExpensive && !$isCombo
            && !$isHot && !$isCold && !$isBreakfast && !$isLunch && !$isDinner
        => "😊 Tôi rất vui được gợi ý cho bạn! Bạn đang muốn:\n\n"
            . "🍔 Đồ ăn nhanh (fast food)\n"
            . "🥗 Đồ ăn healthy (eat clean)\n"
            . "🍜 Cơm / Phở / Bún / Mì\n"
            . "🦐 Hải sản\n"
            . "🌿 Món chay\n"
            . "🍰 Tráng miệng / Đồ ngọt\n"
            . "🧋 Đồ uống\n"
            . "🌶️ Đồ cay / Lẩu\n"
            . "🥪 Ăn vặt / Khai vị\n\n"
            . "Hoặc bạn cũng có thể cho tôi biết bạn đang ăn **bữa sáng / trưa / tối**, muốn món **nóng hay lạnh**, hay ngân sách **tiết kiệm hay cao cấp** nhé!",

        // 2. Đồ ăn nhanh
        $isFastFood    => $replyFastFood,

        // 3. Healthy / Eat clean
        $isHealthy     => $replyHealthy,

        // 4. Đồ uống
        $isDrink       => $replyDrink,

        // 5. Tráng miệng / Đồ ngọt
        $isDessert     => $replyDessert,

        // 6. Cơm / Cháo / Xôi
        $isRice        => $replyRice,

        // 7. Phở / Bún / Mì / Pasta
        $isNoodle      => $replyNoodle,

        // 8. Món chay / Vegan
        $isVeg         => $replyVeg,

        // 9. Đồ cay / Lẩu
        $isSpicy       => $replySpicy,

        // 10. Hải sản
        $isSeafood     => $replySeafood,

        // 11. Ăn vặt / Khai vị / Snack
        $isSnack       => $replySnack,

        // 12. Món rẻ / Tiết kiệm
        $isCheap       => $replyCheap,

        // 13. Món cao cấp / Đặc biệt
        $isExpensive   => $replyExpensive,

        // 14. Combo / Set
        $isCombo       => $replyCombo,

        // 15. Đồ nóng / Ấm
        $isHot         => $replyHot,

        // 16. Đồ lạnh / Mát / Giải nhiệt
        $isCold        => $replyCold,

        // 17. Bữa sáng
        $isBreakfast   => $replyBreakfast,

        // 18. Bữa trưa
        $isLunch       => $replyLunch,

        // 19. Bữa tối
        $isDinner      => $replyDinner,

        // ── CÁC KỊCH BẢN CŨ GIỮ NGUYÊN ─────────────────────────────────────

        // 20. Xem menu
        str_contains($message, 'menu') || str_contains($message, 'món') || str_contains($message, 'hôm nay')
        => "🍔 Menu hôm nay:\n{$menuText}\n\nBạn muốn đặt món nào? Hoặc gõ **gợi ý** để tôi tư vấn nhé!",

        // 21. Khuyến mãi
        str_contains($message, 'khuyến mãi') || str_contains($message, 'giảm giá') || str_contains($message, 'ưu đãi')
        => "🎁 Khuyến mãi hôm nay:\n- Giảm 20% đơn hàng đầu tiên\n- Mua 2 tặng 1 với Burger\n- Miễn phí giao hàng đơn từ 100.000đ",

        // 22. Đơn hàng / Giao hàng
        str_contains($message, 'đơn hàng') || str_contains($message, 'theo dõi') || str_contains($message, 'giao hàng')
        => "🚀 Bạn có thể theo dõi đơn hàng tại mục **Đơn hàng** trên menu.\nThời gian giao hàng trung bình 25-30 phút.",

        // 23. Thanh toán
        str_contains($message, 'thanh toán') || str_contains($message, 'trả tiền')
        => "💳 FoodShop hỗ trợ 2 hình thức:\n- Thanh toán khi nhận hàng (COD)\n- Chuyển khoản QR Code",

        // 24. Liên hệ / Hỗ trợ
        str_contains($message, 'liên hệ') || str_contains($message, 'hỗ trợ') || str_contains($message, 'hotline')
        => "📞 Liên hệ hỗ trợ:\n- Hotline: 1800 1234 (miễn phí)\n- Email: support@foodshop.vn\n- Giờ làm việc: 8h - 22h",

        // 25. Giờ mở cửa
        str_contains($message, 'giờ') || str_contains($message, 'mở cửa')
        => "🕐 FoodShop mở cửa từ **8:00 - 22:00** tất cả các ngày trong tuần.",

        // 26. Chào hỏi
        str_contains($message, 'xin chào') || str_contains($message, 'hello')
            || str_contains($message, 'hi') || str_contains($message, 'chào')
        => "👋 Xin chào! Tôi là FoodBot, trợ lý của FoodShop.\nTôi có thể giúp bạn:\n- 🍔 Xem menu & đặt món\n- 😊 Gợi ý món theo sở thích\n- 🎁 Thông tin khuyến mãi\n- 🚀 Theo dõi đơn hàng\n- 💳 Hỗ trợ thanh toán\n\nBạn cần gì hôm nay?",

        // 27. Mặc định
        default
        => "Xin lỗi, tôi chưa hiểu câu hỏi của bạn 😅\nBạn có thể hỏi về:\n- 🍔 Menu & Gợi ý món ăn\n- 🎁 Khuyến mãi\n- 🚀 Theo dõi đơn hàng\n- 💳 Thanh toán\n- 📞 Liên hệ hỗ trợ",
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
| 2.5.1. AVATAR — Upload & Xóa ảnh đại diện
|--------------------------------------------------------------------------
*/

Route::post('/profile/avatar', function (Request $request) {
    if (!session('user')) {
        return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
    }

    $request->validate(
        ['avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'],
        [
            'avatar.required' => 'Vui lòng chọn ảnh.',
            'avatar.image'    => 'File phải là ảnh.',
            'avatar.mimes'    => 'Chỉ chấp nhận ảnh JPG, PNG, GIF hoặc WebP.',
            'avatar.max'      => 'Ảnh không được vượt quá 2MB.',
        ]
    );

    $user = User::find(session('user')['id']);

    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
        Storage::disk('public')->delete($user->avatar);
    }

    $path = $request->file('avatar')->store('avatars', 'public');
    $user->update(['avatar' => $path]);

    session()->put('user', [
        'id'     => $user->id,
        'name'   => $user->name,
        'email'  => $user->email,
        'avatar' => $path,
    ]);

    return response()->json([
        'success'    => true,
        'avatar_url' => asset('storage/' . $path),
    ]);
});

Route::post('/profile/avatar/remove', function () {
    if (!session('user')) {
        return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
    }

    $user = User::find(session('user')['id']);

    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
        Storage::disk('public')->delete($user->avatar);
    }

    $user->update(['avatar' => null]);

    session()->put('user', [
        'id'    => $user->id,
        'name'  => $user->name,
        'email' => $user->email,
    ]);

    return response()->json(['success' => true]);
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

Route::get('/chat/poll', function (Request $request) {
    if (!session('user')) return response()->json(['error' => 'Unauthorized'], 401);
    $userId  = session('user')['id'];
    $lastId  = $request->query('last_id', 0);
    $msgs    = Message::where('user_id', $userId)
                      ->where('id', '>', $lastId)
                      ->orderBy('created_at')
                      ->get();
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

    Route::get('/chat/{userId}/poll', function (Request $request, $userId) {
        $lastId = $request->query('last_id', 0);
        $msgs   = Message::where('user_id', $userId)
                         ->where('id', '>', $lastId)
                         ->orderBy('created_at')
                         ->get();
        Message::where('user_id', $userId)->where('sender', 'user')->where('is_read', false)
               ->update(['is_read' => true]);
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