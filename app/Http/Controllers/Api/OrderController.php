<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    public function checkout(Request $request)
    {

        //Prendi carrello utente
        $cartItems=CartItem::with('product')
                            ->where('user_id', Auth::id())
                            ->get();
        
        //Verifica carrello se vuoto o no
        if ($cartItems->isEmpty()){
            return response()->json(['message'=> 'Carrello Vuoto'], 400);
        }
        
            //Calcola Totale
        $total=$cartItems->sum(function($item){
            return $item->product->price * $item->quantity;
            });

        //transazione db (tutto o niente)
        DB::beginTransaction();

            try {

            // ✅ CREA ORDER (qui nasce $order)
            $order = Order::create([
            'user_id' => Auth::id(),
            'total'   => $total,
            'status'  => 'pending', // o quello che usi tu
            'shipping_address' => 'Indirizzo non ancora fornito',
            ]);
                //crea order_items
                foreach ($cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id'=> $order->id,
                        'product_id'=> $cartItem->product_id,
                        'quantity'=> $cartItem->quantity,
                        'price_snapshot'=>$cartItem->product->price //prezzo dell'ordine
                    ]);
                }
                //svuotiamo il carrello
                CartItem::where('user_id', Auth::id())->delete();

                DB::commit();

                //Ritorna order con items
                return response()->json([
                    'message'=> 'Ordine Creato con successo',
                    'order'=> $order->load('orderItems.product')
                    ], 201);
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Checkout error', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
                return response()->json(['message'=>'Errore creazione ordine'], 500);
            }
    }

    //Lista ordini utente
    public function index()
    {
        $orders=Order::with('orderItems.product')
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json(['orders'=> $orders]);

    }

    //Dettaglio singolo ordine
    public function show($id)
    {
        $order=Order::with('orderItems.product')
                        ->where('user_id', Auth::id())
                        ->findorFail($id);

        return response()->json(['order'=> $order]);

    }







    
}
