<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $first_name
 * @property string $last_name
 * @property string|null $company
 * @property string|null $vat_number
 * @property string|null $tax_code
 * @property string $address
 * @property string|null $address_2
 * @property string $city
 * @property string $province
 * @property string $postal_code
 * @property string $country
 * @property string|null $phone
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_address
 * @property-read string $full_name
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereTaxCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereVatNumber($value)
 */
	class Address extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $logo
 * @property string|null $website
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $activeProducts
 * @property-read int|null $active_products_count
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand withoutTrashed()
 */
	class Brand extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property string|null $discount_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $items_count
 * @property-read float $subtotal
 * @property-read float $total
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart forSession($sessionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart forUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereDiscountCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUserId($value)
 */
	class Cart extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cart $cart
 * @property-read int $max_quantity
 * @property-read float $subtotal
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariant|null $productVariant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereUpdatedAt($value)
 */
	class CartItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int|null $parent_id
 * @property string|null $image
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read string $full_path
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category rootCategories()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withActiveProducts()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withoutTrashed()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string $type
 * @property numeric $value
 * @property numeric|null $min_order_amount
 * @property int|null $usage_limit
 * @property bool $is_single_use_per_user
 * @property int $usage_count
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Brand> $brands
 * @property-read int|null $brands_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DiscountUsage> $usages
 * @property-read int|null $usages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount byCode(string $code)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereIsSingleUsePerUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereMinOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUsageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereValue($value)
 */
	class Discount extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $discount_id
 * @property int $user_id
 * @property int|null $order_id
 * @property \Illuminate\Support\Carbon $used_at
 * @property-read \App\Models\Discount $discount
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountUsage whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountUsage whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountUsage whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountUsage whereUserId($value)
 */
	class DiscountUsage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $invoice_number
 * @property string $type
 * @property int|null $order_id
 * @property int|null $user_id
 * @property string $client_name
 * @property string|null $client_email
 * @property string|null $client_company
 * @property string|null $client_vat_number
 * @property string|null $client_tax_code
 * @property string|null $client_sdi_code
 * @property string|null $client_pec
 * @property string|null $client_address
 * @property string|null $client_city
 * @property string|null $client_province
 * @property string|null $client_postal_code
 * @property string $client_country
 * @property numeric $subtotal
 * @property numeric $discount_amount
 * @property numeric $tax_amount
 * @property numeric $total
 * @property string $status
 * @property string|null $payment_method
 * @property \Illuminate\Support\Carbon $issue_date
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $paid_date
 * @property string|null $notes
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice byStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientPec($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientSdiCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientTaxCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaidDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice withoutTrashed()
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property int|null $product_id
 * @property string $description
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $vat_rate
 * @property numeric $discount_percentage
 * @property numeric $subtotal
 * @property numeric $tax_amount
 * @property numeric $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice|null $invoice
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereDiscountPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereVatRate($value)
 */
	class InvoiceItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $order_number
 * @property int|null $user_id
 * @property string|null $guest_email
 * @property string $shipping_first_name
 * @property string $shipping_last_name
 * @property string|null $shipping_company
 * @property string $shipping_address
 * @property string|null $shipping_address_2
 * @property string $shipping_city
 * @property string $shipping_province
 * @property string $shipping_postal_code
 * @property string $shipping_country
 * @property string|null $shipping_phone
 * @property bool $billing_same_as_shipping
 * @property string|null $billing_first_name
 * @property string|null $billing_last_name
 * @property string|null $billing_company
 * @property string|null $billing_vat_number
 * @property string|null $billing_tax_code
 * @property string|null $billing_address
 * @property string|null $billing_address_2
 * @property string|null $billing_city
 * @property string|null $billing_province
 * @property string|null $billing_postal_code
 * @property string|null $billing_country
 * @property string|null $billing_phone
 * @property string|null $billing_sdi_code
 * @property string|null $billing_pec
 * @property numeric $subtotal
 * @property numeric $discount_amount
 * @property string|null $discount_code
 * @property numeric $shipping_cost
 * @property numeric $tax_amount
 * @property numeric $total
 * @property string $payment_method
 * @property string $payment_status
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $payment_transaction_id
 * @property string $status
 * @property string $source
 * @property string $return_status
 * @property string|null $return_reason
 * @property \Illuminate\Support\Carbon|null $return_requested_at
 * @property \Illuminate\Support\Carbon|null $return_completed_at
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $notes
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $billing_full_name
 * @property-read string $customer_email
 * @property-read string $shipping_full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransportDocument> $transportDocuments
 * @property-read int|null $transport_documents_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order bySource(string $source)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order cancelled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order delivered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order processing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order shipped()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingPec($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingSameAsShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingSdiCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingTaxCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBillingVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereGuestEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereReturnCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereReturnReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereReturnRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereReturnStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order withReturns()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order withoutTrashed()
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property int|null $product_variant_id
 * @property string $product_name
 * @property string $product_sku
 * @property string|null $variant_name
 * @property string|null $product_image
 * @property numeric $price
 * @property int $quantity
 * @property numeric $vat_rate
 * @property numeric $subtotal
 * @property numeric $tax_amount
 * @property numeric $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $full_product_name
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariant|null $productVariant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereVariantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereVatRate($value)
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $brand_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $short_description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string $sku
 * @property numeric $price
 * @property numeric|null $compare_at_price
 * @property numeric|null $cost
 * @property int $stock_quantity
 * @property int $low_stock_threshold
 * @property numeric $vat_rate
 * @property string|null $barcode
 * @property numeric|null $weight Peso in grammi
 * @property string|null $dimensions LxPxH in cm
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $manage_stock
 * @property string|null $main_image
 * @property int $order
 * @property int $views_count
 * @property int $sales_count
 * @property numeric $average_rating
 * @property-read int|null $reviews_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Brand|null $brand
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Discount> $discounts
 * @property-read int|null $discounts_count
 * @property-read int $approved_reviews_count
 * @property-read float|null $discount_percentage
 * @property-read float|null $margin_amount
 * @property-read float|null $margin
 * @property-read float $price_with_vat
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockLog> $stockLogs
 * @property-read int|null $stock_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductVariant> $variants
 * @property-read int|null $variants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wishlist> $wishlists
 * @property-read int|null $wishlists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product bestSellers(int $minSales = 10)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product byCategory($categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product inStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product lowStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product outOfStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product topRated(float $minRating = 4)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereAverageRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCompareAtPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDimensions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereLowStockThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMainImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereManageStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereReviewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSalesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStockQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereVatRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property string $image
 * @property string|null $alt_text
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage whereAltText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductImage whereUpdatedAt($value)
 */
	class ProductImage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property string $name
 * @property string $sku
 * @property string|null $barcode
 * @property int $stock_quantity
 * @property string|null $image
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockLog> $stockLogs
 * @property-read int|null $stock_logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant inStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereStockQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariant withoutTrashed()
 */
	class ProductVariant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property int|null $order_id
 * @property int $rating
 * @property string|null $title
 * @property string|null $comment
 * @property bool $is_verified_purchase
 * @property bool $is_approved
 * @property int $helpful_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review byRating(int $rating)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review recent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review verifiedPurchases()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereHelpfulCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereIsVerifiedPurchase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $product_id
 * @property int|null $product_variant_id
 * @property string $type
 * @property int $quantity
 * @property int $quantity_before
 * @property int $quantity_after
 * @property string|null $reason
 * @property int|null $reference_id
 * @property string|null $reference_type
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $cancelled_by
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read string $type_label
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariant|null $productVariant
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog forProduct(int $productId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog forVariant(int $variantId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereCancelledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereQuantityAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereQuantityBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereReferenceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereUserId($value)
 */
	class StockLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $company_name
 * @property string $slug
 * @property string|null $contact_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $vat_number
 * @property string|null $tax_code
 * @property string|null $address
 * @property string|null $city
 * @property string|null $province
 * @property string|null $postal_code
 * @property string $country
 * @property string|null $website
 * @property string|null $payment_terms
 * @property string|null $notes
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $full_address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SupplierOrder> $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereTaxCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier withoutTrashed()
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $order_number
 * @property int $supplier_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $expected_delivery_date
 * @property \Illuminate\Support\Carbon|null $received_date
 * @property numeric $subtotal
 * @property numeric $tax_rate
 * @property numeric $tax_amount
 * @property numeric $shipping_cost
 * @property numeric $total
 * @property string|null $notes
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SupplierOrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Supplier|null $supplier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereExpectedDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereReceivedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrder withoutTrashed()
 */
	class SupplierOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $supplier_order_id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property int $quantity_ordered
 * @property int $quantity_received
 * @property numeric $unit_price
 * @property numeric $total_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $remaining_quantity
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariant|null $productVariant
 * @property-read \App\Models\SupplierOrder|null $supplierOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereQuantityOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereQuantityReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereSupplierOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierOrderItem whereUpdatedAt($value)
 */
	class SupplierOrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $document_number
 * @property int|null $order_id
 * @property int|null $user_id
 * @property string $sender_name
 * @property string|null $sender_address
 * @property string|null $sender_city
 * @property string|null $sender_province
 * @property string|null $sender_postal_code
 * @property string $recipient_name
 * @property string $recipient_address
 * @property string $recipient_city
 * @property string $recipient_province
 * @property string $recipient_postal_code
 * @property string $recipient_country
 * @property string $shipping_method
 * @property string|null $carrier_name
 * @property string|null $tracking_number
 * @property int $packages_count
 * @property numeric|null $total_weight
 * @property string|null $appearance Aspetto esteriore beni
 * @property string $reason
 * @property \Illuminate\Support\Carbon $shipping_date
 * @property \Illuminate\Support\Carbon|null $delivery_date
 * @property string $status
 * @property string|null $notes
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read string $reason_label
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransportDocumentItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument byStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereAppearance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereCarrierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereDocumentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument wherePackagesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereRecipientAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereRecipientCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereRecipientCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereRecipientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereRecipientPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereRecipientProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereSenderAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereSenderCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereSenderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereSenderPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereSenderProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereShippingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereShippingMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereTotalWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocument withoutTrashed()
 */
	class TransportDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $transport_document_id
 * @property int|null $product_id
 * @property int|null $product_variant_id
 * @property string $description
 * @property int $quantity
 * @property string $unit
 * @property numeric|null $weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariant|null $productVariant
 * @property-read \App\Models\TransportDocument|null $transportDocument
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereTransportDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransportDocumentItem whereWeight($value)
 */
	class TransportDocumentItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string $role
 * @property numeric $welcome_voucher
 * @property bool $newsletter_subscribed
 * @property bool $is_active
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Models\Cart|null $cart
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wishlist> $wishlists
 * @property-read int|null $wishlists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User customers()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newsletterSubscribed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNewsletterSubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWelcomeVoucher($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser, \Filament\Models\Contracts\HasName {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist whereUserId($value)
 */
	class Wishlist extends \Eloquent {}
}

