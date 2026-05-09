<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Be Vietnam Pro', sans-serif; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.35s ease forwards; }

        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 50;
            backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active { display: flex; }

        /* QR pulse animation */
        @keyframes pulse-border {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
            50% { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
        }
        .qr-pulse { animation: pulse-border 2s infinite; }

        /* Countdown */
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 251; }
        }
        .countdown-circle { animation: countdown 180s linear forwards; }

        .payment-option {
            border: 2px solid #e2e8f0;
            border-radius: 0.875rem;
            padding: 0.875rem 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .payment-option:has(input:checked) {
            border-color: #f59e0b;
            background: #fffbeb;
        }
        .payment-option input { accent-color: #f59e0b; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

<!-- NAVBAR -->
<nav class="bg-white border-b border-slate-100 shadow-sm sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
        <a href="/" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-amber-400 rounded-lg flex items-center justify-center">
                <i class="fas fa-utensils text-slate-900 text-xs"></i>
            </div>
            <span class="font-extrabold text-slate-800 tracking-wide">FOOD<span class="text-amber-400">.</span></span>
        </a>
        <div class="flex items-center gap-5 text-sm font-medium text-slate-500">
            <a href="/" class="hover:text-amber-500 transition flex items-center gap-1.5">
                <i class="fas fa-home text-xs"></i> Trang chủ
            </a>
            <a href="/orders" class="hover:text-amber-500 transition flex items-center gap-1.5">
                <i class="fas fa-receipt text-xs"></i> Đơn hàng
            </a>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto px-6 py-10">

    <!-- Flash -->
    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl fade-in">
        <i class="fas fa-check-circle text-emerald-500"></i>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(count($cart) > 0)
    @php $total = 0; @endphp

    <h1 class="text-2xl font-extrabold text-slate-800 mb-6">Giỏ hàng của bạn</h1>

    <div class="flex flex-col lg:flex-row gap-8">

        <!-- CART TABLE -->
        <div class="flex-1">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-bold text-slate-700">Danh sách món</h2>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($cart as $id => $item)
                    @php
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    @endphp
                    <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/50 transition">
                        <!-- Ảnh -->
                        <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 shadow-sm bg-slate-100">
                            <img src="{{ asset($item['image']) }}" class="w-full h-full object-cover" alt="{{ $item['name'] }}">
                        </div>
                        <!-- Tên -->
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 truncate">{{ $item['name'] }}</p>
                            <p class="text-sm text-slate-400 mt-0.5">{{ number_format($item['price']) }}đ / phần</p>
                        </div>
                        <!-- Số lượng -->
                        <div class="flex items-center gap-2">
                            <form action="/cart/update/{{ $id }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="decrease">
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition text-sm">−</button>
                            </form>
                            <span class="w-8 text-center font-bold text-slate-700">{{ $item['quantity'] }}</span>
                            <form action="/cart/update/{{ $id }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="increase">
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition text-sm">+</button>
                            </form>
                        </div>
                        <!-- Subtotal -->
                        <div class="w-24 text-right">
                            <p class="font-bold text-slate-800">{{ number_format($subtotal) }}<span class="text-xs font-normal text-slate-400">đ</span></p>
                        </div>
                        <!-- Xóa -->
                        <form action="/cart/remove/{{ $id }}" method="POST">
                            @csrf
                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 hover:bg-red-50 hover:text-red-500 transition">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>

            <a href="/" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-amber-500 transition">
                <i class="fas fa-arrow-left text-xs"></i> Tiếp tục mua sắm
            </a>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-24">
                <h2 class="font-bold text-slate-800 mb-5">Tóm tắt đơn hàng</h2>

                <div class="space-y-3 mb-5 pb-5 border-b border-slate-100">
                    @foreach($cart as $id => $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 truncate mr-2">{{ $item['name'] }} <span class="text-slate-400">×{{ $item['quantity'] }}</span></span>
                        <span class="font-medium text-slate-700 flex-shrink-0">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center mb-6">
                    <span class="font-bold text-slate-700">Tổng cộng</span>
                    <span class="text-xl font-extrabold text-amber-500">{{ number_format($total) }}<span class="text-sm font-normal text-slate-400">đ</span></span>
                </div>

                <!-- PHƯƠNG THỨC THANH TOÁN -->
                <div class="mb-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Phương thức thanh toán</p>
                    <div class="space-y-2">
                        <label class="payment-option">
                            <input type="radio" name="payment_display" value="cod" checked onchange="handlePaymentChange(this)">
                            <span class="text-lg">💵</span>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Thanh toán khi nhận</p>
                                <p class="text-xs text-slate-400">COD - Trả tiền mặt</p>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_display" value="online" onchange="handlePaymentChange(this)">
                            <span class="text-lg">💳</span>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Thanh toán online</p>
                                <p class="text-xs text-slate-400">QR Code - Chuyển khoản</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- NÚT ĐẶT HÀNG -->
                <form action="/checkout" method="POST" id="checkoutForm">
                    @csrf
                    <input type="hidden" name="payment_method" id="payment_method_input" value="cod">
                    <button type="button" onclick="handleCheckout()"
                        class="w-full bg-slate-900 text-white font-bold py-3.5 rounded-xl hover:bg-slate-700 transition text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-lock text-xs"></i> Đặt hàng ngay
                    </button>
                </form>
            </div>
        </div>

    </div>

    @else
    <div class="text-center py-24 fade-in">
        <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-shopping-cart text-slate-300 text-3xl"></i>
        </div>
        <h2 class="text-xl font-bold text-slate-600 mb-2">Giỏ hàng trống</h2>
        <p class="text-slate-400 text-sm mb-6">Thêm vài món ngon vào giỏ nhé!</p>
        <a href="/" class="bg-amber-400 text-slate-900 font-bold px-8 py-3 rounded-xl hover:bg-amber-500 transition inline-flex items-center gap-2">
            <i class="fas fa-utensils text-sm"></i> Xem thực đơn
        </a>
    </div>
    @endif

</div>

<!-- QR MODAL -->
<div id="qrModal" class="modal-overlay" onclick="closeQR(event)">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden" onclick="event.stopPropagation()">

        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold opacity-80 uppercase tracking-wide">Thanh toán online</p>
                    <p class="text-xl font-extrabold mt-0.5">{{ number_format($total ?? 0) }}<span class="text-sm font-normal opacity-80">đ</span></p>
                </div>
                <button onclick="document.getElementById('qrModal').classList.remove('active')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/20 hover:bg-white/30 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Bank info -->
            <div class="bg-slate-50 rounded-2xl p-4 mb-5 text-sm">
                <div class="flex justify-between mb-2">
                    <span class="text-slate-400">Ngân hàng</span>
                    <span class="font-bold text-slate-700">MB Bank</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-slate-400">Số tài khoản</span>
                    <span class="font-bold text-slate-700 font-mono">1234 5678 9012</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-slate-400">Chủ tài khoản</span>
                    <span class="font-bold text-slate-700">FOOD STORE</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nội dung CK</span>
                    <span class="font-bold text-emerald-600">DH{{ rand(10000,99999) }}</span>
                </div>
            </div>

            <!-- QR Code (giả định bằng SVG pattern) -->
            <div class="flex justify-center mb-4">
                <div class="qr-pulse rounded-2xl p-3 border-2 border-emerald-400 bg-white inline-block">
                    <svg width="160" height="160" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg">
                        <!-- QR pattern giả định -->
                        <rect width="160" height="160" fill="white"/>
                        <!-- Top-left finder -->
                        <rect x="10" y="10" width="40" height="40" rx="4" fill="#1e293b"/>
                        <rect x="16" y="16" width="28" height="28" rx="2" fill="white"/>
                        <rect x="22" y="22" width="16" height="16" rx="1" fill="#1e293b"/>
                        <!-- Top-right finder -->
                        <rect x="110" y="10" width="40" height="40" rx="4" fill="#1e293b"/>
                        <rect x="116" y="16" width="28" height="28" rx="2" fill="white"/>
                        <rect x="122" y="22" width="16" height="16" rx="1" fill="#1e293b"/>
                        <!-- Bottom-left finder -->
                        <rect x="10" y="110" width="40" height="40" rx="4" fill="#1e293b"/>
                        <rect x="16" y="116" width="28" height="28" rx="2" fill="white"/>
                        <rect x="22" y="122" width="16" height="16" rx="1" fill="#1e293b"/>
                        <!-- Data modules (giả định) -->
                        <rect x="58" y="10" width="8" height="8" fill="#1e293b"/>
                        <rect x="70" y="10" width="8" height="8" fill="#1e293b"/>
                        <rect x="82" y="10" width="8" height="8" fill="#1e293b"/>
                        <rect x="58" y="22" width="8" height="8" fill="#1e293b"/>
                        <rect x="70" y="34" width="8" height="8" fill="#1e293b"/>
                        <rect x="82" y="22" width="8" height="8" fill="#1e293b"/>
                        <rect x="94" y="34" width="8" height="8" fill="#1e293b"/>
                        <rect x="58" y="46" width="8" height="8" fill="#1e293b"/>
                        <rect x="82" y="46" width="8" height="8" fill="#1e293b"/>
                        <rect x="10" y="58" width="8" height="8" fill="#1e293b"/>
                        <rect x="22" y="58" width="8" height="8" fill="#1e293b"/>
                        <rect x="46" y="58" width="8" height="8" fill="#1e293b"/>
                        <rect x="58" y="58" width="8" height="8" fill="#1e293b"/>
                        <rect x="70" y="58" width="8" height="8" fill="#1e293b"/>
                        <rect x="94" y="58" width="8" height="8" fill="#1e293b"/>
                        <rect x="106" y="58" width="8" height="8" fill="#1e293b"/>
                        <rect x="130" y="58" width="8" height="8" fill="#1e293b"/>
                        <rect x="142" y="58" width="8" height="8" fill="#1e293b"/>
                        <rect x="10" y="70" width="8" height="8" fill="#1e293b"/>
                        <rect x="34" y="70" width="8" height="8" fill="#1e293b"/>
                        <rect x="58" y="70" width="8" height="8" fill="#1e293b"/>
                        <rect x="82" y="70" width="8" height="8" fill="#1e293b"/>
                        <rect x="106" y="70" width="8" height="8" fill="#1e293b"/>
                        <rect x="118" y="70" width="8" height="8" fill="#1e293b"/>
                        <rect x="142" y="70" width="8" height="8" fill="#1e293b"/>
                        <rect x="22" y="82" width="8" height="8" fill="#1e293b"/>
                        <rect x="46" y="82" width="8" height="8" fill="#1e293b"/>
                        <rect x="70" y="82" width="8" height="8" fill="#1e293b"/>
                        <rect x="94" y="82" width="8" height="8" fill="#1e293b"/>
                        <rect x="118" y="82" width="8" height="8" fill="#1e293b"/>
                        <rect x="130" y="82" width="8" height="8" fill="#1e293b"/>
                        <rect x="10" y="94" width="8" height="8" fill="#1e293b"/>
                        <rect x="34" y="94" width="8" height="8" fill="#1e293b"/>
                        <rect x="58" y="94" width="8" height="8" fill="#1e293b"/>
                        <rect x="82" y="94" width="8" height="8" fill="#1e293b"/>
                        <rect x="106" y="94" width="8" height="8" fill="#1e293b"/>
                        <rect x="142" y="94" width="8" height="8" fill="#1e293b"/>
                        <rect x="58" y="106" width="8" height="8" fill="#1e293b"/>
                        <rect x="70" y="106" width="8" height="8" fill="#1e293b"/>
                        <rect x="94" y="118" width="8" height="8" fill="#1e293b"/>
                        <rect x="106" y="106" width="8" height="8" fill="#1e293b"/>
                        <rect x="118" y="118" width="8" height="8" fill="#1e293b"/>
                        <rect x="130" y="106" width="8" height="8" fill="#1e293b"/>
                        <rect x="142" y="118" width="8" height="8" fill="#1e293b"/>
                        <rect x="58" y="118" width="8" height="8" fill="#1e293b"/>
                        <rect x="82" y="130" width="8" height="8" fill="#1e293b"/>
                        <rect x="70" y="142" width="8" height="8" fill="#1e293b"/>
                        <rect x="94" y="130" width="8" height="8" fill="#1e293b"/>
                        <rect x="106" y="142" width="8" height="8" fill="#1e293b"/>
                        <rect x="118" y="130" width="8" height="8" fill="#1e293b"/>
                        <rect x="142" y="142" width="8" height="8" fill="#1e293b"/>
                        <!-- Center logo -->
                        <rect x="66" y="66" width="28" height="28" rx="4" fill="white"/>
                        <rect x="68" y="68" width="24" height="24" rx="3" fill="#f59e0b"/>
                        <text x="80" y="85" text-anchor="middle" font-size="13" font-weight="bold" fill="#1e293b">F</text>
                    </svg>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mb-5">Quét mã QR bằng app ngân hàng để thanh toán</p>

            <!-- Confirm button — submit form thật -->
            <button onclick="confirmQRPayment()"
                class="w-full bg-emerald-500 text-white font-bold py-3.5 rounded-xl hover:bg-emerald-600 transition text-sm flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Đã thanh toán xong
            </button>

            <button onclick="document.getElementById('qrModal').classList.remove('active')"
                class="w-full mt-2 py-2.5 rounded-xl text-slate-400 hover:bg-slate-50 transition text-sm font-medium">
                Quay lại
            </button>
        </div>
    </div>
</div>

<script>
function handlePaymentChange(radio) {
    document.getElementById('payment_method_input').value = radio.value;
}

function handleCheckout() {
    const method = document.getElementById('payment_method_input').value;
    if (method === 'online') {
        // Hiện QR modal thay vì submit ngay
        document.getElementById('qrModal').classList.add('active');
    } else {
        // COD thì submit thẳng
        document.getElementById('checkoutForm').submit();
    }
}

function confirmQRPayment() {
    // Đã xác nhận thanh toán QR → submit form
    document.getElementById('qrModal').classList.remove('active');
    document.getElementById('checkoutForm').submit();
}

function closeQR(e) {
    if (e.target === document.getElementById('qrModal')) {
        document.getElementById('qrModal').classList.remove('active');
    }
}
</script>

</body>
</html>