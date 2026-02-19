<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|UnitEnum|null $navigationGroup = 'Ordini';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Ordine';
    
    protected static ?string $pluralModelLabel = 'Ordini';

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Ordine')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Numero Ordine')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('user_id')
                            ->label('Cliente')
                            ->relationship('user', 'email')
                            ->preload()
                            ->disabled(),

                        Forms\Components\TextInput::make('guest_email')
                            ->label('Email Guest')
                            ->email()
                            ->visible(fn ($record) => !$record?->user_id)
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('Stato Ordine')
                            ->options([
                                'pending' => 'In Attesa',
                                'processing' => 'In Elaborazione',
                                'shipped' => 'Spedito',
                                'delivered' => 'Consegnato',
                                'cancelled' => 'Annullato',
                            ])
                            ->disableOptionWhen(fn (string $value): bool => $value === 'delivered' || $value === 'cancelled'),

                        Forms\Components\Select::make('payment_status')
                            ->label('Stato Pagamento')
                            ->options([
                                'pending' => 'In Attesa',
                                'paid' => 'Pagato',
                                'failed' => 'Fallito',
                                'refunded' => 'Rimborsato',
                            ])
                            ->disabled(),

                        Forms\Components\Select::make('payment_method')
                            ->label('Metodo Pagamento')
                            ->options([
                                'credit_card' => 'Carta di Credito',
                                'paypal' => 'PayPal',
                                'bank_transfer' => 'Bonifico Bancario',
                            ])
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Importi')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotale')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Sconto')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('Spedizione')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label('IVA')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        Forms\Components\TextInput::make('total')
                            ->label('Totale')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),
                    ])
                    ->columns(5),

                Section::make('Note')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Note Cliente')
                            ->rows(2)
                            ->disabled(),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Note Admin')
                            ->rows(2),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('N. Ordine')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->tooltip('Clicca per copiare')
                    ->copyMessage('N. ordine copiato!'),

                Tables\Columns\TextColumn::make('customer_email')
                    ->label('Cliente')
                    ->searchable(['users.email', 'guest_email'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Totale')
                    ->money('EUR')
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Stato Pagamento')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'In Attesa',
                        'paid' => 'Pagato',
                        'failed' => 'Fallito',
                        'refunded' => 'Rimborsato',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'In Attesa',
                        'processing' => 'In Elaborazione',
                        'shipped' => 'Spedito',
                        'delivered' => 'Consegnato',
                        'cancelled' => 'Annullato',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'shipped' => 'warning',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Articoli')
                    ->counts('items')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato Ordine')
                    ->placeholder('Tutti')
                    ->options([
                        'pending' => 'In Attesa',
                        'processing' => 'In Elaborazione',
                        'shipped' => 'Spedito',
                        'delivered' => 'Consegnato',
                        'cancelled' => 'Annullato',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Stato Pagamento')
                    ->placeholder('Tutti')
                    ->options([
                        'pending' => 'In Attesa',
                        'paid' => 'Pagato',
                        'failed' => 'Fallito',
                        'refunded' => 'Rimborsato',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Dal'),
                        DatePicker::make('created_until')
                            ->label('Al'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->color('info'),
                    
                    Action::make('download_invoice')
                        ->label('Scarica Fattura')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function (Order $record) {
                            return response()->streamDownload(function () use ($record) {
                                echo Pdf::loadView('pdf.invoice', ['order' => $record])->output();
                            }, "fattura-{$record->order_number}.pdf");
                        }),
                    
                    Action::make('mark_as_shipped')
                        ->label('Segna come Spedito')
                        ->icon('heroicon-o-truck')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (Order $record) => $record->status === 'processing')
                        ->action(fn (Order $record) => $record->markAsShipped()),
                    
                    Action::make('mark_as_delivered')
                        ->label('Segna come Consegnato')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Order $record) => $record->status === 'shipped')
                        ->action(fn (Order $record) => $record->markAsDelivered()),
                ]),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                        ->label('Esporta Selezionati')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(OrderExporter::class),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Esporta Tutti')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(OrderExporter::class),
            ])
            ->emptyStateHeading('Nessun ordine trovato')
            ->emptyStateDescription('Gli ordini dei clienti appariranno qui.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() > 10 ? 'danger' : 'warning';
    }
}