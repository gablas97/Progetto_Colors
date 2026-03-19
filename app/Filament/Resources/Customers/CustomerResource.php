<?php


namespace App\Filament\Resources\Customers;

use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|UnitEnum|null $navigationGroup = 'Clienti';
    protected static ?string $navigationLabel = 'Clienti';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clienti';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'customer');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dati Personali')
                ->schema([
                    Forms\Components\TextInput::make('first_name')->label('Nome')->required()->maxLength(255),
                    Forms\Components\TextInput::make('last_name')->label('Cognome')->required()->maxLength(255),
                    Forms\Components\TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('phone')->label('Telefono')->maxLength(20),
                ])->columns(2),
            Section::make('Impostazioni')
                ->schema([
                    Forms\Components\Toggle::make('is_active')->label('Attivo')->default(true),
                    Forms\Components\Toggle::make('newsletter_subscribed')->label('Iscritto Newsletter')->default(false),
                ])->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dati Personali')
                ->schema([
                    Infolists\Components\TextEntry::make('first_name')->label('Nome'),
                    Infolists\Components\TextEntry::make('last_name')->label('Cognome'),
                    Infolists\Components\TextEntry::make('email')->label('Email')->copyable(),
                    Infolists\Components\TextEntry::make('phone')->label('Telefono')
                        ->default('—'),
                ])->columns(2),
            Section::make('Impostazioni')
                ->schema([
                    Infolists\Components\IconEntry::make('newsletter_subscribed')
                        ->label('Iscritto Newsletter')
                        ->boolean(),
                    Infolists\Components\IconEntry::make('is_active')
                        ->label('Attivo')
                        ->boolean(),
                ])->columns(2),
            Section::make('Attività')
                ->schema([
                    Infolists\Components\TextEntry::make('orders_count')
                        ->label('Ordini Totali')
                        ->state(fn (User $record) => $record->orders()->count()),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Registrato il')
                        ->dateTime('d/m/Y H:i'),
                    Infolists\Components\TextEntry::make('email_verified_at')
                        ->label('Email Verificata')
                        ->dateTime('d/m/Y H:i')
                        ->default('Non verificata'),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nome Completo')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name'])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->tooltip('Clicca per copiare')
                    ->copyMessage('Email copiata!')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefono')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Ordini')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Registrato')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Stato')
                    ->trueLabel('Attivi')->falseLabel('Disattivi'),
                Tables\Filters\TernaryFilter::make('newsletter_subscribed')->label('Newsletter'),
            ])
            ->recordUrl(null)
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'display:none'])
                    ->modalWidth('3xl'),
                ActionGroup::make([
                    EditAction::make()->label('Modifica')->color('warning'),
                    Action::make('toggle_active')
                        ->label(fn (User $record) => $record->is_active ? 'Disattiva' : 'Attiva')
                        ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (User $record) => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (User $record) => $record->is_active ? 'Disattiva cliente' : 'Attiva cliente')
                        ->modalDescription(fn (User $record) => $record->is_active
                            ? 'Il cliente non potrà più accedere al proprio account. Continuare?'
                            : 'Il cliente potrà nuovamente accedere al proprio account. Continuare?'
                        )
                        ->action(fn (User $record) => $record->update(['is_active' => !$record->is_active])),
                    DeleteAction::make()
                        ->label('Elimina')
                        ->before(function (User $record, \Filament\Actions\DeleteAction $action) {
                            if ($record->orders()->exists()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Impossibile eliminare')
                                    ->body('Il cliente ha ordini collegati. Disattiva l\'account invece di eliminarlo.')
                                    ->danger()
                                    ->send();
                                $action->cancel();
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Attiva selezionati')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Attiva clienti selezionati')
                        ->modalDescription('I clienti selezionati verranno attivati. Continuare?')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('deactivate')
                        ->label('Disattiva selezionati')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Disattiva clienti selezionati')
                        ->modalDescription('I clienti selezionati non potranno più accedere al proprio account. Continuare?')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()->label('Elimina selezionati'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) User::where('role', 'customer')->count();
    }
}
