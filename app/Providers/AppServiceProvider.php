<?php

namespace App\Providers;

use App\Settings\MailSettings;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Throwable;

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
        $this->configureDefaults();
        $this->applyDynamicMailSettings();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Config mail.php default dibaca dari .env saat boot framework.
     * Di sini kita override dengan nilai dari database (Settings) setiap request,
     * supaya perubahan lewat panel admin langsung berlaku tanpa deploy ulang.
     */
    protected function applyDynamicMailSettings(): void
    {
        // Guard: tabel settings mungkin belum ada saat instalasi awal / migrate pertama kali.
        if (! Schema::hasTable('settings')) {
            return;
        }

        try {
            $mail = app(MailSettings::class);
        } catch (Throwable $e) {
            return;
        }

        config([
            'mail.default' => $mail->mailer,
            'mail.mailers.smtp.host' => $mail->host,
            'mail.mailers.smtp.port' => $mail->port,
            'mail.mailers.smtp.username' => $mail->username,
            'mail.mailers.smtp.password' => $mail->password,
            'mail.mailers.smtp.encryption' => $mail->encryption ?: null,
            'mail.from.address' => $mail->from_address,
            'mail.from.name' => $mail->from_name,
        ]);
    }
}
