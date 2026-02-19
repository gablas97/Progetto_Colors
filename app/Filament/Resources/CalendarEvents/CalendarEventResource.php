<?php


namespace App\Filament\Resources\CalendarEvents;

use App\Models\CalendarEvent;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CalendarEventResource extends Resource
{
    protected static ?string $model = CalendarEvent::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Agenda';
    protected static ?string $modelLabel = 'Evento';
    protected static ?string $pluralModelLabel = 'Agenda';
    protected static ?int $navigationSort = 2;

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dettagli Evento')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Titolo')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('type')
                        ->label('Tipo')
                        ->options([
                            'pagamento' => 'Pagamento',
                            'scadenza' => 'Scadenza',
                            'commercialista' => 'Commercialista',
                            'consegna' => 'Consegna',
                            'riunione' => 'Riunione',
                            'ordine_fornitore' => 'Ordine Fornitore',
                            'promozione' => 'Promozione',
                            'inventario' => 'Inventario',
                            'altro' => 'Altro',
                        ])
                        ->required()
                        ->default('altro'),
                    Forms\Components\Select::make('priority')
                        ->label('Priorità')
                        ->options([
                            'bassa' => 'Bassa',
                            'media' => 'Media',
                            'alta' => 'Alta',
                            'urgente' => 'Urgente',
                        ])
                        ->default('media')
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Descrizione')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(3),
            Section::make('Data e Ora')
                ->schema([
                    Forms\Components\Toggle::make('all_day')
                        ->label('Tutto il giorno')
                        ->default(false)
                        ->live(),
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Inizio')
                        ->required()
                        ->default(now()),
                    Forms\Components\DateTimePicker::make('ends_at')
                        ->label('Fine')
                        ->after('starts_at'),
                    Forms\Components\Select::make('recurrence')
                        ->label('Ricorrenza')
                        ->options([
                            'none' => 'Nessuna',
                            'daily' => 'Giornaliera',
                            'weekly' => 'Settimanale',
                            'monthly' => 'Mensile',
                            'yearly' => 'Annuale',
                        ])
                        ->default('none'),
                    Forms\Components\DateTimePicker::make('reminder_at')
                        ->label('Promemoria'),
                ])->columns(2),
            Section::make('Note e Stato')
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Note')
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_completed')
                        ->label('Completato')
                        ->default(false),
                    Forms\Components\ColorPicker::make('color')
                        ->label('Colore'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label('')
                    ->width(10),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'pagamento' => 'Pagamento',
                        'scadenza' => 'Scadenza',
                        'commercialista' => 'Commercialista',
                        'consegna' => 'Consegna',
                        'riunione' => 'Riunione',
                        'ordine_fornitore' => 'Ord. Fornitore',
                        'promozione' => 'Promozione',
                        'inventario' => 'Inventario',
                        default => 'Altro',
                    })
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'pagamento' => 'danger',
                        'scadenza' => 'warning',
                        'commercialista' => 'gray',
                        'consegna' => 'info',
                        'riunione' => 'primary',
                        'ordine_fornitore' => 'info',
                        'promozione' => 'success',
                        'inventario' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Priorità')
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'bassa' => 'Bassa',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'bassa' => 'gray',
                        'media' => 'info',
                        'alta' => 'warning',
                        'urgente' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->label('Completato')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'pagamento' => 'Pagamento',
                        'scadenza' => 'Scadenza',
                        'commercialista' => 'Commercialista',
                        'consegna' => 'Consegna',
                        'riunione' => 'Riunione',
                        'ordine_fornitore' => 'Ordine Fornitore',
                        'promozione' => 'Promozione',
                        'inventario' => 'Inventario',
                        'altro' => 'Altro',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priorità')
                    ->options([
                        'bassa' => 'Bassa',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente',
                    ]),
                Tables\Filters\TernaryFilter::make('is_completed')
                    ->label('Stato')
                    ->trueLabel('Completati')
                    ->falseLabel('Da completare'),
            ])
            ->recordActions([
                Action::make('complete')
                    ->label('Completa')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (CalendarEvent $record) => !$record->is_completed)
                    ->action(fn (CalendarEvent $record) => $record->markAsCompleted()),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('starts_at', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalendarEvents::route('/'),
            'create' => Pages\CreateCalendarEvent::route('/create'),
            'edit' => Pages\EditCalendarEvent::route('/{record}/edit'),
            'view' => Pages\ViewCalendarEvent::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = CalendarEvent::where('is_completed', false)
            ->where('starts_at', '<=', now()->addDays(3))
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
