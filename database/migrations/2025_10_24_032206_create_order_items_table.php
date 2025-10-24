<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_order_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items');
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // ราคาต่อหน่วย (ณ ตอนที่สั่ง)
            $table->text('notes')->nullable(); // เช่น "ไม่เผ็ด"
            $table->enum('status', ['pending', 'cooking', 'ready', 'served'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
