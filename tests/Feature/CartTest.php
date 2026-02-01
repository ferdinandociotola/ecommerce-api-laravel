<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class CartTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function user_can_add_product_to_cart()
    {
        $user=User::factory()->create();
        $category=Category::factory()->create();
        $product=Product::factory()->create([
                'category_id'=> $category->id,
                'price'=>100,
                'stock'=> 10
                ]);

        $response=$this->actingAs($user, 'sanctum')
                    ->postJson('/api/cart', [
                        'product_id'=> $product->id,
                        'quantity'=> 2
                        ]);


        $response->assertStatus(200)
                ->assertJson(['message'=> 'Prodotto aggiunto']);

        $this->assertDatabaseHas('cart_items', [
                        'user_id'=> $user->id,
                        'product_id'=> $product->id,
                        'quantity'=> 2
                        ]);
    }


    /** @test */
    public function user_can_view_cart()
    {
        $user=User::factory()->create();
        $category=Category::factory()->create();
        $product=Product::factory()->create([
                        'category_id'=>$category->id,
                        'price'=> 50.00
                        ]);


        $this->actingAs($user, 'sanctum')
                    ->postJson('/api/cart', [
                        'product_id'=> $product->id,
                        'quantity'=> 1
                        ]);

        $response=$this->actingAs($user, 'sanctum')
                        ->getJson('/api/cart');

        $response->assertStatus(200)
                        ->assertJsonStructure([
                        'cart'=> [
                        '*'=>['id', 'product_id', 'name', 'price', 'quantity', 'subtotal']],
                        'total',
                        ]);
    }


    /** @test */
    public function user_cannot_add_product_with_insufficient_stock()
    {
        $user=User::factory()->create();
        $category=Category::factory()->create();
        $product=Product::factory()->create([
                        'category_id'=> $category->id,
                        'stock'=> 5
                        ]);


        $response=$this->actingAs($user, 'sanctum')
                        ->postJson('/api/cart', [
                            'product_id'=> $product->id,
                            'quantity'=> 10
                            ]);


        $response->assertStatus(400)
                        ->assertJson(['message'=> 'Stock insufficiente']);

    }


}
