<?php

namespace App\Filament\Resources\Suppliers;

use App\Models\Supplier;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static string|UnitEnum|null $navigationGroup = 'Fornitori';
    protected static ?string $navigationLabel = 'Fornitori';
    protected static ?string $modelLabel = 'Fornitore';
    protected static ?string $pluralModelLabel = 'Fornitori';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Dati Aziendali')
                ->icon('heroicon-o-building-office')->iconColor('primary')
                ->description('Ragione sociale, referente e dati fiscali')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Ragione Sociale')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    Forms\Components\Hidden::make('slug'),
                    Forms\Components\TextInput::make('contact_name')->label('Referente')->maxLength(255),
                    Forms\Components\TextInput::make('vat_number')->label('Partita IVA')->maxLength(20),
                    Forms\Components\TextInput::make('tax_code')->label('Codice Fiscale')->maxLength(20),
                ])->columns(2),
            Section::make('Contatti')
                ->icon('heroicon-o-phone')->iconColor('info')
                ->description('Email, telefono e sito web')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('email')->label('Email')->email(),
                    Forms\Components\TextInput::make('phone')->label('Telefono'),
                    Forms\Components\TextInput::make('mobile')->label('Cellulare'),
                    Forms\Components\TextInput::make('website')->label('Sito Web')->url(),
                ])->columns(2),
            Section::make('Indirizzo')
                ->icon('heroicon-o-map-pin')->iconColor('info')
                ->description('Sede legale o operativa')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('address')->label('Indirizzo'),
                    Forms\Components\TextInput::make('city')->label('Città'),
                    Forms\Components\TextInput::make('province')->label('Provincia')->maxLength(2),
                    Forms\Components\TextInput::make('postal_code')->label('CAP'),
                ])->columns(2),
            Section::make('Altre Informazioni')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')->iconColor('gray')
                ->description('Termini di pagamento, stato e note')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('payment_terms')->label('Termini Pagamento'),
                    Forms\Components\Toggle::make('is_active')->label('Attivo')->default(true),
                    Forms\Components\Textarea::make('notes')->label('Note')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Dati Aziendali')
                ->icon('heroicon-o-building-office')->iconColor('primary')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('company_name')->label('Ragione Sociale')->weight('bold')->size('lg'),
                    TextEntry::make('contact_name')->label('Referente')->placeholder('—'),
                    TextEntry::make('vat_number')->label('Partita IVA')->placeholder('—')->copyable(),
                    TextEntry::make('tax_code')->label('Codice Fiscale')->placeholder('—')->copyable(),
                ])->columns(2),

            Section::make('Contatti')
                ->icon('heroicon-o-phone')->iconColor('info')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('email')->label('Email')->placeholder('—')->copyable()->color('primary'),
                    TextEntry::make('phone')->label('Telefono')->placeholder('—'),
                    TextEntry::make('mobile')->label('Cellulare')->placeholder('—'),
                    TextEntry::make('website')->label('Sito Web')->placeholder('—'),
                ])->columns(2),

            Section::make('Indirizzo')
                ->icon('heroicon-o-map-pin')->iconColor('info')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('full_address')
                        ->label('Indirizzo Completo')
                        ->getStateUsing(fn (Supplier $record): string => $record->full_address ?: '—')
                        ->columnSpanFull(),
                ]),

            Section::make('Altre Informazioni')
                ->icon('heroicon-o-cog-6-tooth')->iconColor('gray')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('payment_terms')->label('Termini di Pagamento')->placeholder('—'),
                    IconEntry::make('is_active')->label('Attivo')->boolean()->trueColor('success')->falseColor('danger'),
                ])->columns(2),

            Section::make('Note')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')->iconColor('gray')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('notes')->hiddenLabel()->placeholder('Nessuna nota')->columnSpanFull(),
                ])->collapsible()->collapsed(fn (Supplier $record) => !$record->notes),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')->label('Ragione Sociale')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_name')->label('Referente')->searchable()->placeholder('—'),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->placeholder('—'),
                Tables\Columns\TextColumn::make('phone')->label('Telefono')->placeholder('—'),
                Tables\Columns\TextColumn::make('city')->label('Città')->searchable()->placeholder('—'),
                Tables\Columns\TextColumn::make('orders_count')->label('Ordini')->counts('orders')->badge()->sortable()->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')->label('Attivo')->boolean()->alignCenter(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Stato')
                    ->placeholder('Tutti')
                    ->trueLabel('Attivi')
                    ->falseLabel('Disattivi'),
                Tables\Filters\TrashedFilter::make()
                    ->label('Mostra Eliminati')
                    ->placeholder('No')
                    ->trueLabel('Si')
                    ->falseLabel('Solo Eliminati'),
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
                        ->label(fn (Supplier $record) => $record->is_active ? 'Disattiva' : 'Attiva')
                        ->icon(fn (Supplier $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (Supplier $record) => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Supplier $record) => $record->is_active ? 'Disattiva fornitore' : 'Attiva fornitore')
                        ->modalDescription(fn (Supplier $record) => $record->is_active
                            ? 'Il fornitore verrà disattivato. Continuare?'
                            : 'Il fornitore verrà riattivato. Continuare?'
                        )
                        ->action(fn (Supplier $record) => $record->update(['is_active' => !$record->is_active])),
                    RestoreAction::make()->label('Ripristina'),
                    DeleteAction::make()
                        ->label('Elimina')
                        ->modalHeading('Elimina fornitore')
                        ->modalDescription('Il fornitore verrà eliminato definitivamente. Continuare?')
                        ->before(function (Supplier $record, \Filament\Actions\DeleteAction $action) {
                            $activeOrders = $record->orders()
                                ->whereNotIn('status', ['received', 'cancelled'])
                                ->exists();
                            if ($activeOrders) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Impossibile eliminare')
                                    ->body('Il fornitore ha ordini attivi (bozza, inviati o confermati). Completa o annulla gli ordini prima di eliminare il fornitore.')
                                    ->danger()
                                    ->send();
                                $action->cancel();
                            }
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()->label('Ripristina selezionati'),
                    DeleteBulkAction::make()->label('Elimina selezionati'),
                ])->label('Azioni'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit'   => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }
}
