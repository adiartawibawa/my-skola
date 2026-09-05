<?php

namespace App\Providers;

use App\Models\ClassRoomStudent;
use App\Observers\SyncUserAccountOnEnrollmentStatusChangeObserver;
use App\Settings\AppSettings;
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
        if (! $this->isRunningDatabaseSetupCommand()) {
            $this->configureDefaults();
            $this->applyDynamicMailSettings();
            $this->applyDynamicAppSettings();
        }
        ClassRoomStudent::observe(SyncUserAccountOnEnrollmentStatusChangeObserver::class);
    }

    /**
     * Hindari membaca Settings dari database saat sedang menjalankan
     * command yang justru sedang MEMBANGUN/MENGUBAH struktur database
     * itu sendiri — menghindari race condition "tabel settings ada,
     * tapi baris datanya belum sempat ter-seed migration lain".
     */
    protected function isRunningDatabaseSetupCommand(): bool
    {
        if (! $this->app->runningInConsole()) {
            return false;
        }

        $command = $_SERVER['argv'][1] ?? null;

        return in_array($command, [
            'migrate',
            'migrate:fresh',
            'migrate:refresh',
            'migrate:rollback',
            'migrate:reset',
            'migrate:install',
            'db:seed',
            'db:wipe',
        ], true);
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
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

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

    protected function applyDynamicAppSettings(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $app = app(AppSettings::class);
        } catch (Throwable $e) {
            return;
        }

        date_default_timezone_set($app->timezone);

        config([
            'app.timezone' => $app->timezone,
            'app.locale' => $app->locale,
        ]);

        app()->setLocale($app->locale);
    }
}
