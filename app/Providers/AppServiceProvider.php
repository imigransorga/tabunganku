<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @rupiah($value) -> "Rp 1.500.000"
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float) ($expression), 0, ',', '.'); ?>";
        });

        // Buat kategori default saat user baru mendaftar.
        User::created(function (User $user) {
            $defaults = [
                ['Gaji', 'income', '#22c55e'],
                ['Bonus', 'income', '#16a34a'],
                ['Makan', 'expense', '#ef4444'],
                ['Transport', 'expense', '#f59e0b'],
                ['Tagihan', 'expense', '#8b5cf6'],
                ['Belanja', 'expense', '#ec4899'],
                ['Lainnya', 'expense', '#6b7280'],
            ];
            foreach ($defaults as [$name, $type, $color]) {
                $user->categories()->create(compact('name', 'type', 'color'));
            }
        });
    }
}
