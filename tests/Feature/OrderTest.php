<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\CartItem;

class OrderTest extends TestCase
{
    
    use RefreshDatabase;

    /** @test */
    public function user_can_checkout()
    {
        $user=User::factory()->create();
        $category=Category::factory()->create();
        $product=Product::factory()->create([
                    'category_id'=> $category->id,
                    'price'=> 100,
                    'stock'=> 10
                    ]);


        //Aggiungo prodotto al carrello
        CartItem::create([
                'user_id'=>$user->id,
                'product_id'=> $product->id,
                'quantity'=> 2
                ]);

        $response=$this->actingAs($user, 'sanctum')
                    ->postJson('/api/checkout');

        $response->assertStatus(201)
                    ->assertJsonStructure([
                        'message',
                        'order'=> ['id', 'total', 'status']
                        ]);

        $this->assertDatabaseHas('orders', [
                    'user_id'=> $user->id,
                    'total'=> 200.00,
                    'status'=> 'pending'
                    ]);

        $this->assertDatabaseMissing('cart_items', [
                    'user_id'=> $user->id
                    ]);
    }


    /** @test */
    public function user_cannot_checkout_with_empty_cart()
    {
        $user=User::factory()->create();
        
        $response=$this->actingAs($user, 'sanctum')
                        ->postJson('/api/checkout');

        $response->assertStatus(400)
                    ->assertJson(['message'=> 'Carrello Vuoto']);

    }

    /** @test */
    public function user_can_view_orders()
    {
        $user=User::factory()->create();
        $category=Category::factory()->create();
        $product=Product::factory()->create([
                    'category_id'=> $category->id
                    ]);

        CartItem::create([
                    'user_id' => $user->id,
                    'product_id'=> $product->id,
                    'quantity'=> 1
                    ]);


        //creaiamo l'ordine
        $this->actingAs($user, 'sanctum')->postJson('/api/checkout');

        //visualizziamo ordini
        $response=$this->actingAs($user, 'sanctum')
                        ->getJson('/api/orders');

        $response->assertStatus(200)
                        ->assertJsonStructure([
                            'orders'=>[
                                '*'=> ['id', 'total', 'status', 'order_items']]
                                ]);     

    }



}
