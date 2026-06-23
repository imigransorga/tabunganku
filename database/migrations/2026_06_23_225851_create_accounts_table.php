<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');                       // contoh: BCA Utama
            $table->enum('type', ['bank', 'cash', 'ewallet', 'savings'])->default('bank');
            $table->string('bank_name')->nullable();      // nama bank
            $table->string('account_number')->nullable(); // nomor rekening
            $table->decimal('opening_balance', 18, 2)->default(0); // saldo awal
            $table->decimal('balance', 18, 2)->default(0);         // saldo berjalan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
