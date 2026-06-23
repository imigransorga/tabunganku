<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 18, 2);
            $table->date('date');
            $table->string('description')->nullable();
            // alur pengajuan: pending -> approved/rejected. Saldo hanya berubah saat approved.
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
