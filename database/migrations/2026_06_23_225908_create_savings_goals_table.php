<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // akun tempat dana tabungan ditampung (opsional)
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');                                   // contoh: Dana Darurat
            $table->decimal('target_amount', 18, 2)->default(0);      // target total
            $table->enum('frequency', ['daily', 'weekly', 'monthly']); // wajib harian/mingguan/bulanan
            $table->decimal('amount_per_period', 18, 2);             // nominal wajib per periode
            $table->date('start_date');
            $table->date('target_date')->nullable();
            $table->enum('status', ['active', 'completed', 'paused'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_goals');
    }
};
