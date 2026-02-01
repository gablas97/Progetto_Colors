<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_users_are_seeded_correctly(): void
    {
        $this->assertEquals(4, User::count());
        $this->assertEquals(1, User::admins()->count());
        $this->assertEquals(3, User::customers()->count());
    }

    public function test_categories_hierarchy_works(): void
    {
        $cancelleria = Category::where('slug', 'cancelleria')->first();
        
        $this->assertNotNull($cancelleria);
        $this->assertEquals(3, $cancelleria->children->count());
        $this->assertNull($cancelleria->parent_id);
    }

    public function test_products_with_variants_are_created(): void
    {
        $quaderno = Product::where('sku', 'QUAD-A4-RIG-80')->first();
        
        $this->assertNotNull($quaderno);
        $this->assertEquals(4, $quaderno->variants->count());
        $this->assertFalse($quaderno->manage_stock);
    }

    public function test_product_scopes_work(): void
    {
        $this->assertEquals(8, Product::active()->count());
        $this->assertEquals(3, Product::featured()->count());
    }

    public function test_orders_are_created_with_items(): void
    {
        $this->assertEquals(5, Order::count());
        
        $order = Order::first();
        $this->assertNotNull($order->order_number);
        $this->assertTrue($order->items->count() > 0);
        $this->assertTrue($order->total > 0);
    }

    public function test_cart_relationships_work(): void
    {
        $user = User::where('email', 'mario.rossi@example.com')->first();
        $cart = Cart::forUser($user->id)->first();
        
        $this->assertNotNull($cart);
        $this->assertTrue($cart->items->count() > 0);
    }

    public function test_product_helpers_work(): void
    {
        $quaderno = Product::where('sku', 'QUAD-A4-RIG-80')->first();
        
        $this->assertTrue($quaderno->hasDiscount());
        $this->assertEquals(22, $quaderno->discount_percentage);
        $this->assertEquals(4.27, $quaderno->price_with_vat);
    }

    public function test_reviews_update_product_ratings(): void
    {
        $quaderno = Product::where('sku', 'QUAD-A4-RIG-80')->first();
        
        $this->assertEquals(2, $quaderno->reviews_count);
        $this->assertEquals(4.50, $quaderno->average_rating);
    }
}