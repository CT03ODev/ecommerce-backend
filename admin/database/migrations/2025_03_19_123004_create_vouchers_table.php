<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage']); // Giảm giá cố định hoặc theo phần trăm
            $table->decimal('discount_value', 10, 2); // Giá trị giảm giá
            $table->decimal('minimum_spend', 10, 2)->nullable(); // Giá trị đơn hàng tối thiểu để áp dụng
            $table->decimal('maximum_discount', 10, 2)->nullable(); // Giới hạn số tiền giảm tối đa (cho loại percentage)
            $table->integer('usage_limit')->nullable(); // Giới hạn số lần sử dụng
            $table->integer('usage_count')->default(0); // Số lần đã sử dụng
            $table->timestamp('start_date')->nullable(); // Ngày bắt đầu
            $table->timestamp('end_date')->nullable(); // Ngày kết thúc
            $table->boolean('is_active')->default(true);
            
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });

        // Thêm cột voucher_id vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('address_id');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn('voucher_id');
        });
        
        Schema::dropIfExists('vouchers');
    }
};