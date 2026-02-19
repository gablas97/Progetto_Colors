<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('order_number')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'id'),
                TextInput::make('guest_email')
                    ->email(),
                TextInput::make('shipping_first_name')
                    ->required(),
                TextInput::make('shipping_last_name')
                    ->required(),
                TextInput::make('shipping_company'),
                TextInput::make('shipping_address')
                    ->required(),
                TextInput::make('shipping_address_2'),
                TextInput::make('shipping_city')
                    ->required(),
                TextInput::make('shipping_province')
                    ->required(),
                TextInput::make('shipping_postal_code')
                    ->required(),
                TextInput::make('shipping_country')
                    ->required()
                    ->default('IT'),
                TextInput::make('shipping_phone')
                    ->tel(),
                Toggle::make('billing_same_as_shipping')
                    ->required(),
                TextInput::make('billing_first_name'),
                TextInput::make('billing_last_name'),
                TextInput::make('billing_company'),
                TextInput::make('billing_vat_number'),
                TextInput::make('billing_tax_code'),
                TextInput::make('billing_address'),
                TextInput::make('billing_address_2'),
                TextInput::make('billing_city'),
                TextInput::make('billing_province'),
                TextInput::make('billing_postal_code'),
                TextInput::make('billing_country'),
                TextInput::make('billing_phone')
                    ->tel(),
                TextInput::make('billing_sdi_code'),
                TextInput::make('billing_pec'),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('discount_code'),
                TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                Select::make('payment_method')
                    ->options(['credit_card' => 'Credit card', 'paypal' => 'Paypal', 'bank_transfer' => 'Bank transfer'])
                    ->required(),
                Select::make('payment_status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded'])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('paid_at'),
                TextInput::make('payment_transaction_id'),
                Select::make('status')
                    ->options([
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('shipped_at'),
                DateTimePicker::make('delivered_at'),
                DateTimePicker::make('cancelled_at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
            ]);
    }
}
