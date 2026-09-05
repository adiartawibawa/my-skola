<?php

namespace App\Filament\Resources\GuardianStudents;

use App\Enums\GuardianRelationshipType;
use App\Filament\Resources\GuardianStudents\Pages\ManageGuardianStudents;
use App\Models\GuardianStudent;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class GuardianStudentResource extends Resource
{
    protected static ?string $model = GuardianStudent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Data Orang Tua';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Akun Orang Tua')
                    ->options(fn () => User::query()->where('role', 'parent')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('student_id')
                    ->label('Siswa')
                    ->relationship('student.user', 'name')
                    ->searchable()
                    ->required(),

                Select::make('relationship_type')
                    ->options(GuardianRelationshipType::options())
                    ->required(),

                DatePicker::make('verified_at')
                    ->label('Diverifikasi Pada')
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guardian.name')
                    ->label('Orang Tua')
                    ->searchable(),

                TextColumn::make('student.user.name')
                    ->label('Siswa')
                    ->searchable(),

                TextColumn::make('student.nisn')
                    ->label('NISN'),

                TextColumn::make('relationship_type')
                    ->formatStateUsing(fn (GuardianRelationshipType $state) => $state->label())
                    ->badge(),

                TextColumn::make('verified_at')
                    ->label('Diverifikasi')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum diverifikasi'),

                TextColumn::make('created_at')
                    ->label('Ditautkan Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('relationship_type')->options(GuardianRelationshipType::options()),
            ])
            ->recordActions([
                // EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGuardianStudents::route('/'),
        ];
    }
}
