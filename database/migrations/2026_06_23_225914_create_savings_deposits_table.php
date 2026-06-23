<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('savings_goal_id')->constrained()->cascadeOnDelete();
            // akun sumber dana setoran (saldo akun ini berkurang)
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 18, 2);
            $table->date('date');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['savings_goal_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_deposits');
    }
};
