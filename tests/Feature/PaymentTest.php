<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\CartItem;
use App\Models\Order;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_payment_intent()
    {

        $user=User::factory()->create();
        $category=Category::factory()->create();
        $product=Product::factory()->create([
                        'category_id'=> $category->id,
                        'price'=> 100
                        ]);


        CartItem::create([
                        'user_id'=>$user->id,
                        'product_id'=> $product->id,
                        'quantity'=>1
                        ]);

        // Crea ordine
        $checkoutResponse = $this->actingAs($user, 'sanctum')
                                 ->postJson('/api/checkout');


        $orderId=$checkoutResponse->json('order.id');

        //crea payment intent verso stripe reale in test mode
        $response =$this->actingAs($user, 'sanctum')
                        ->postJson('/api/payment/create-intent', [
                            'order_id'=> $orderId
                        ]);

        $response->assertStatus(200)
                        ->assertJsonStructure([
                                'client_secret',
                                'payment_intent_id'
                                ]);
    }


    /** @test  */
    public function cannot_create_payment_intent_for_already_paid_order()
    {
        $user=User::factory()->create();

        $order=Order::create([
                'user_id'=> $user->id,
                'total'=> 100,
                'status'=> 'confirmed',
                'payment_status'=> 'paid' //già pagato
                ]);

        $response=$this->actingAs($user, 'sanctum')
                        ->postJson('/api/payment/create-intent', [
                                'order_id'=> $order->id
                                ]);

        $response->assertStatus(400)
         ->assertJson(['message' => 'Ordine già pagato']);


    }




}
