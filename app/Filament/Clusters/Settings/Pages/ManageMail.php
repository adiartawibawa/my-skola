<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\MailSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ManageMail extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string $settings = MailSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Mail Server';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konfigurasi SMTP')
                    ->columns(2)
                    ->components([
                        Select::make('mailer')
                            ->options([
                                'smtp' => 'SMTP',
                                'sendmail' => 'Sendmail',
                                'log' => 'Log (untuk testing, tidak benar-benar terkirim)',
                            ])
                            ->required()
                            ->live(),

                        TextInput::make('host')
                            ->label('Host')
                            ->required(fn ($get) => $get('mailer') === 'smtp')
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),

                        TextInput::make('port')
                            ->numeric()
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),

                        Select::make('encryption')
                            ->label('Enkripsi')
                            ->options(['tls' => 'TLS', 'ssl' => 'SSL', '' => 'Tanpa Enkripsi'])
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),

                        TextInput::make('username')
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),

                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),
                    ]),

                Section::make('Pengirim Default')
                    ->columns(2)
                    ->components([
                        TextInput::make('from_address')->label('Email Pengirim')->email()->required(),
                        TextInput::make('from_name')->label('Nama Pengirim')->required(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label('Kirim Email Uji Coba')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->schema([
                    TextInput::make('to')
                        ->label('Kirim ke')
                        ->email()
                        ->required()
                        ->default(auth()->user()->email),
                ])
                ->action(function (array $data) {
                    try {
                        Mail::raw('Ini email uji coba dari pengaturan Mail Server.', function ($message) use ($data) {
                            $message->to($data['to'])->subject('Uji Coba Konfigurasi Email');
                        });

                        Notification::make()->title('Email uji coba berhasil dikirim.')->success()->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Gagal mengirim email')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    // public static function canAccess(): bool
    // {
    //     return auth()->user()?->role->isAdmin() ?? false;
    // }
}
