<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            // NULL = không có voucher, số > 0 = giá voucher đang áp dụng
            $table->unsignedBigInteger('voucher_price')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn('voucher_price');
        });
    }
};