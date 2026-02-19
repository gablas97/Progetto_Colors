<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\WarehouseMovement;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use UnitEnum;

class CaricoMerce extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-on-square';
    protected static string|UnitEnum|null $navigationGroup = 'Magazzino';
    protected static ?string $navigationLabel = 'Carico Merce';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Carico Merce';
    protected string $view = 'filament.pages.warehouse-operation';

    public ?array $data = [];
    public string $operationType = 'carico';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Scansiona o Cerca Prodotto')
                ->schema([
                    Forms\Components\TextInput::make('barcode')
                        ->label('Codice a Barre')
                        ->placeholder('Scansiona o digita il codice a barre...')
                        ->autofocus()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            if ($state) {
                                $product = Product::where('barcode', $state)->first();
                                $variant = ProductVariant::where('barcode', $state)->first();
                                if ($variant) {
                                    $set('product_id', $variant->product_id);
                                    $set('product_variant_id', $variant->id);
                                } elseif ($product) {
                                    $set('product_id', $product->id);
                                    $set('product_variant_id', null);
                                }
                            }
                        }),
                    Forms\Components\Select::make('product_id')
                        ->label('Prodotto')
                        ->options(fn() => Product::where('is_active', true)->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),
                    Forms\Components\Select::make('product_variant_id')
                        ->label('Variante')
                        ->options(function (Get $get) {
                            $productId = $get('product_id');
                            if (!$productId) return [];
                            return ProductVariant::where('product_id', $productId)->pluck('name', 'id');
                        })
                        ->visible(fn (Get $get) => $get('product_id') && ProductVariant::where('product_id', $get('product_id'))->exists()),
                ])->columns(3),
            Section::make('Dettagli Carico')
                ->schema([
                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantità')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(1),
                    Forms\Components\Select::make('reason')
                        ->label('Causale')
                        ->options([
                            'acquisto_fornitore' => 'Acquisto da Fornitore',
                            'reso_cliente' => 'Reso Cliente',
                            'inventario' => 'Inventario',
                            'aggiustamento' => 'Aggiustamento',
                            'trasferimento' => 'Trasferimento',
                        ])
                        ->default('acquisto_fornitore')
                        ->required(),
                    Forms\Components\TextInput::make('unit_cost')
                        ->label('Costo Unitario')
                        ->numeric()
                        ->prefix('€'),
                    Forms\Components\TextInput::make('batch_number')
                        ->label('N. Lotto'),
                    Forms\Components\TextInput::make('reference_number')
                        ->label('Rif. (DDT, Fattura, ecc.)'),
                    Forms\Components\Textarea::make('notes')
                        ->label('Note')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(3),
        ])->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        WarehouseMovement::register([
            'type' => 'carico',
            'reason' => $data['reason'],
            'product_id' => $data['product_id'],
            'product_variant_id' => $data['product_variant_id'] ?? null,
            'quantity' => abs($data['quantity']),
            'unit_cost' => $data['unit_cost'] ?? null,
            'batch_number' => $data['batch_number'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        Notification::make()
            ->title('Carico registrato con successo')
            ->success()
            ->send();

        $this->form->fill();
    }
}
