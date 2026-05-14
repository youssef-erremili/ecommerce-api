<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_price', 10);
            $table->enum('payment_method', array_column(PaymentMethod::cases(), 'value'))->default(PaymentMethod::BANK_TRANSFER->value);
            $table->enum('payment_status', array_column(PaymentStatus::cases(), 'value'))->default(PaymentStatus::UNPAID->value);
            $table->enum('status', array_column(OrderStatus::cases(), 'value'))->default(OrderStatus::PENDING->value);
            $table->string('payment_id')->nullable();
            $table->string('shipping_address');
            $table->string('phone_number');
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
