<?php


namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\WarehouseMovement;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;

class ResoMerce extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static string|UnitEnum|null $navigationGroup = 'Magazzino';
    protected static ?string $navigationLabel = 'Reso Merce';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Reso Merce';
    protected string $view = 'filament.pages.warehouse-operation';

    public ?array $data = [];
    public string $operationType = 'reso';

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
                                }
                            }
                        }),
                    Forms\Components\Select::make('product_id')
                        ->label('Prodotto')
                        ->options(fn() => Product::where('is_active', true)->pluck('name', 'id'))
                        ->searchable()->preload()->required()->live(),
                    Forms\Components\Select::make('product_variant_id')
                        ->label('Variante')
                        ->options(function (Get $get) {
                            $productId = $get('product_id');
                            if (!$productId) return [];
                            return ProductVariant::where('product_id', $productId)->pluck('name', 'id');
                        })
                        ->visible(fn (Get $get) => $get('product_id') && ProductVariant::where('product_id', $get('product_id'))->exists()),
                ])->columns(3),
            Section::make('Dettagli Reso')
                ->schema([
                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantità')->numeric()->required()->minValue(1)->default(1),
                    Forms\Components\Select::make('reason')
                        ->label('Causale')
                        ->options([
                            'reso_cliente' => 'Reso da Cliente',
                            'reso_fornitore' => 'Reso a Fornitore',
                        ])
                        ->default('reso_cliente')->required(),
                    Forms\Components\TextInput::make('reference_number')->label('Rif. Ordine/DDT'),
                    Forms\Components\Textarea::make('notes')->label('Motivo del Reso')->rows(2)->columnSpanFull(),
                ])->columns(3),
        ])->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $type = $data['reason'] === 'reso_fornitore' ? 'scarico' : 'carico';

        WarehouseMovement::register([
            'type' => $type,
            'reason' => $data['reason'],
            'product_id' => $data['product_id'],
            'product_variant_id' => $data['product_variant_id'] ?? null,
            'quantity' => abs($data['quantity']),
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        Notification::make()->title('Reso registrato con successo')->success()->send();
        $this->form->fill();
    }
}
