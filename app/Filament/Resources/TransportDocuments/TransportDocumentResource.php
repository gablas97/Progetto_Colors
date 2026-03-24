<?php

namespace App\Filament\Resources\TransportDocuments;

use App\Models\TransportDocument;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class TransportDocumentResource extends Resource
{
    protected static ?string $model = TransportDocument::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static string|UnitEnum|null $navigationGroup = 'Fatturazione';
    protected static ?string $navigationLabel = 'Documenti di Trasporto';
    protected static ?string $modelLabel = 'DDT';
    protected static ?string $pluralModelLabel = 'Documenti di Trasporto';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Dati DDT')
                ->icon('heroicon-o-document-arrow-down')->iconColor('primary')
                ->description('Numero, stato, causale e ordine collegato')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('document_number')
                        ->label('Numero DDT')
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Generato automaticamente'),
                    Forms\Components\Select::make('order_id')
                        ->label('Ordine Collegato')
                        ->relationship('order', 'order_number')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('status')
                        ->label('Stato')
                        ->options(['draft' => 'Bozza', 'ready' => 'Pronto', 'shipped' => 'Spedito', 'delivered' => 'Consegnato'])
                        ->default('draft'),
                    Forms\Components\Select::make('reason')->label('Causale')
                        ->options([
                            'vendita'           => 'Vendita',
                            'reso'              => 'Reso',
                            'conto_lavorazione' => 'Conto Lavorazione',
                            'omaggio'           => 'Omaggio',
                            'riparazione'       => 'Riparazione',
                            'altro'             => 'Altro',
                        ])->default('vendita')->required(),
                ])->columns(2),

            Section::make('Mittente')
                ->icon('heroicon-o-building-office')->iconColor('gray')
                ->description('Dati del mittente — lascia invariato per Colors S.r.l.')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('sender_name')->label('Ragione Sociale')->default('Colors S.r.l.'),
                    Forms\Components\TextInput::make('sender_address')->label('Indirizzo'),
                    Forms\Components\TextInput::make('sender_city')->label('Città'),
                    Forms\Components\TextInput::make('sender_province')->label('Prov.')->maxLength(2),
                    Forms\Components\TextInput::make('sender_postal_code')->label('CAP'),
                ])->columns(2)->collapsible(),

            Section::make('Destinatario')
                ->icon('heroicon-o-map-pin')->iconColor('info')
                ->description('Dati del destinatario della merce')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('recipient_name')->label('Destinatario')->required(),
                    Forms\Components\TextInput::make('recipient_address')->label('Indirizzo')->required(),
                    Forms\Components\TextInput::make('recipient_city')->label('Città')->required(),
                    Forms\Components\TextInput::make('recipient_province')->label('Prov.')->maxLength(2)->required(),
                    Forms\Components\TextInput::make('recipient_postal_code')->label('CAP')->required(),
                ])->columns(2),

            Section::make('Trasporto')
                ->icon('heroicon-o-truck')->iconColor('warning')
                ->description('Mezzo, corriere, tracking e date di spedizione')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\Select::make('shipping_method')->label('Mezzo di Trasporto')
                        ->options(['corriere' => 'Corriere', 'ritiro' => 'Ritiro', 'proprio_mezzo' => 'Proprio Mezzo'])
                        ->default('corriere'),
                    Forms\Components\TextInput::make('carrier_name')->label('Corriere'),
                    Forms\Components\TextInput::make('tracking_number')->label('Tracking'),
                    Forms\Components\TextInput::make('packages_count')->label('N. Colli')->numeric()->default(1),
                    Forms\Components\TextInput::make('total_weight')->label('Peso Totale (kg)')->numeric(),
                    Forms\Components\TextInput::make('appearance')->label('Aspetto Esteriore Beni'),
                    Forms\Components\DateTimePicker::make('shipping_date')->label('Data Spedizione')->required()->default(now()),
                    Forms\Components\DateTimePicker::make('delivery_date')->label('Data Consegna'),
                ])->columns(2),

            Section::make('Articoli')
                ->icon('heroicon-o-cube')->iconColor('primary')
                ->description('Beni trasportati')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->hiddenLabel()
                        ->schema([
                            Forms\Components\Select::make('product_id')->label('Prodotto')
                                ->relationship('product', 'name')->searchable()->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    if ($state) {
                                        $product = \App\Models\Product::find($state);
                                        if ($product) {
                                            $set('description', $product->name);
                                            $set('weight', $product->weight);
                                        }
                                    }
                                })->columnSpan(2),
                            Forms\Components\TextInput::make('description')->label('Descrizione')->required()->columnSpan(2),
                            Forms\Components\TextInput::make('quantity')->label('Qtà')->numeric()->required()->default(1),
                            Forms\Components\TextInput::make('unit')->label('U.M.')->default('pz'),
                            Forms\Components\TextInput::make('weight')->label('Peso (g)')->numeric(),
                        ])
                        ->columns(3)
                        ->defaultItems(1)
                        ->addActionLabel('Aggiungi Articolo'),
                ]),

            Section::make('Note')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')->iconColor('gray')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\Textarea::make('notes')->label('Note')->hiddenLabel(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Dati DDT')
                ->icon('heroicon-o-document-arrow-down')->iconColor('primary')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('document_number')
                        ->label('Numero DDT')
                        ->copyable()
                        ->weight('bold'),
                    TextEntry::make('reason')
                        ->label('Causale')
                        ->badge()
                        ->color('info')
                        ->formatStateUsing(fn (string $state) => match ($state) {
                            'vendita'           => 'Vendita',
                            'reso'              => 'Reso',
                            'conto_lavorazione' => 'Conto Lavorazione',
                            'omaggio'           => 'Omaggio',
                            'riparazione'       => 'Riparazione',
                            default             => ucfirst($state),
                        }),
                    TextEntry::make('status')
                        ->label('Stato')
                        ->badge()
                        ->formatStateUsing(fn (string $state) => match ($state) {
                            'draft'     => 'Bozza',
                            'ready'     => 'Pronto',
                            'shipped'   => 'Spedito',
                            'delivered' => 'Consegnato',
                            default     => $state,
                        })
                        ->color(fn ($state) => match ($state) {
                            'draft'     => 'gray',
                            'ready'     => 'warning',
                            'shipped'   => 'info',
                            'delivered' => 'success',
                            default     => 'gray',
                        }),
                    TextEntry::make('order.order_number')
                        ->label('Ordine Collegato')
                        ->copyable()
                        ->placeholder('—'),
                ])->columns(2),

            Section::make('Mittente → Destinatario')
                ->icon('heroicon-o-arrows-right-left')->iconColor('info')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('sender_name')
                        ->label('Mittente')
                        ->formatStateUsing(fn ($state, TransportDocument $record): string => implode(', ', array_filter([
                            $record->sender_name,
                            $record->sender_address,
                            $record->sender_postal_code . ' ' . $record->sender_city,
                            $record->sender_province,
                        ])) ?: '—'),
                    TextEntry::make('recipient_name')
                        ->label('Destinatario')
                        ->weight('bold')
                        ->formatStateUsing(fn ($state, TransportDocument $record): string => implode(', ', array_filter([
                            $record->recipient_name,
                            $record->recipient_address,
                            $record->recipient_postal_code . ' ' . $record->recipient_city,
                            $record->recipient_province,
                        ])) ?: '—'),
                ])->columns(2),

            Section::make('Trasporto')
                ->icon('heroicon-o-truck')->iconColor('warning')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('shipping_method')
                        ->label('Mezzo')
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'corriere'      => 'Corriere',
                            'ritiro'        => 'Ritiro',
                            'proprio_mezzo' => 'Proprio Mezzo',
                            default         => '—',
                        }),
                    TextEntry::make('carrier_name')->label('Corriere')->placeholder('—'),
                    TextEntry::make('tracking_number')->label('Tracking')->placeholder('—')->copyable()->color('primary'),
                    TextEntry::make('packages_count')->label('N. Colli'),
                    TextEntry::make('total_weight')->label('Peso Tot. (kg)')->placeholder('—'),
                    TextEntry::make('appearance')->label('Aspetto Beni')->placeholder('—'),
                    TextEntry::make('shipping_date')->label('Data Spedizione')->dateTime('d/m/Y H:i'),
                    TextEntry::make('delivery_date')->label('Data Consegna')->dateTime('d/m/Y H:i')->placeholder('—'),
                ])->columns(4),

            Section::make('Articoli')
                ->icon('heroicon-o-cube')->iconColor('primary')
                ->columnSpanFull()
                ->schema([
                    RepeatableEntry::make('items')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('description')->label('Descrizione'),
                            TextEntry::make('quantity')->label('Qtà'),
                            TextEntry::make('unit')->label('U.M.'),
                            TextEntry::make('weight')->label('Peso (g)')->placeholder('—'),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),
                ]),

            Section::make('Note')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')->iconColor('gray')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('notes')->hiddenLabel()->placeholder('Nessuna nota')->columnSpanFull(),
                ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document_number')
                    ->label('N. DDT')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable()
                    ->tooltip('Clicca per copiare'),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Destinatario')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Causale')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'vendita'           => 'Vendita',
                        'reso'              => 'Reso',
                        'conto_lavorazione' => 'C/Lavorazione',
                        'omaggio'           => 'Omaggio',
                        default             => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('status')->label('Stato')
                    ->badge()
                    ->alignCenter()
                    ->toggleable()
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'ready'     => 'warning',
                        'shipped'   => 'info',
                        'delivered' => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft'     => 'Bozza',
                        'ready'     => 'Pronto',
                        'shipped'   => 'Spedito',
                        'delivered' => 'Consegnato',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('shipping_date')
                    ->label('Data Spedizione')
                    ->dateTime('d/m/Y')
                    ->toggleable()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Ordine')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('—'),
            ])
            ->defaultSort('shipping_date', 'desc')
            ->recordUrl(null)
            ->recordAction(ViewAction::class)
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Stato')
                    ->options(['draft' => 'Bozza', 'ready' => 'Pronto', 'shipped' => 'Spedito', 'delivered' => 'Consegnato']),
                Tables\Filters\SelectFilter::make('reason')->label('Causale')
                    ->options([
                        'vendita'           => 'Vendita',
                        'reso'              => 'Reso',
                        'conto_lavorazione' => 'C/Lavorazione',
                        'omaggio'           => 'Omaggio',
                        'riparazione'       => 'Riparazione',
                        'altro'             => 'Altro',
                    ]),
                Tables\Filters\TrashedFilter::make()
                    ->label('Mostra Eliminati')
                    ->placeholder('No')
                    ->trueLabel('Si')
                    ->falseLabel('Solo Eliminati'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'display:none'])
                    ->modalHeading(fn (TransportDocument $record) => "Dettagli DDT {$record->document_number}")
                    ->modalWidth('5xl'),
                ActionGroup::make([
                    Action::make('download_pdf')
                        ->label('Scarica PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (TransportDocument $record) {
                            $record->load('items.product');
                            $pdf = Pdf::loadView('pdf.transport-document', ['ddt' => $record]);
                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                "{$record->document_number}.pdf"
                            );
                        }),
                    RestoreAction::make()->label('Ripristina'),
                    DeleteAction::make()->label('Elimina'),
                    EditAction::make()
                        ->label('Modifica')
                        ->color('warning'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_download_pdfs')
                        ->label('Scarica PDF selezionati')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            $tempFile = tempnam(sys_get_temp_dir(), 'ddt_');
                            $zip = new \ZipArchive();
                            $zip->open($tempFile, \ZipArchive::OVERWRITE);

                            foreach ($records as $record) {
                                $record->load('items.product');
                                $pdf = Pdf::loadView('pdf.transport-document', ['ddt' => $record]);
                                $zip->addFromString("{$record->document_number}.pdf", $pdf->output());
                            }

                            $zip->close();

                            return response()->streamDownload(function () use ($tempFile) {
                                echo file_get_contents($tempFile);
                                unlink($tempFile);
                            }, 'DDT-' . now()->format('Y-m-d') . '.zip');
                        })
                        ->deselectRecordsAfterCompletion(),
                    RestoreBulkAction::make()->label('Ripristina selezionati'),
                    DeleteBulkAction::make()->label('Elimina selezionati'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTransportDocuments::route('/'),
            'create' => Pages\CreateTransportDocument::route('/create'),
            'edit'   => Pages\EditTransportDocument::route('/{record}/edit'),
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
