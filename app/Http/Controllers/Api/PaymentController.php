<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    //Creaiamo Payment Intent
    public function createPaymentIntent(Request $request)
    {

        $request->validate([
            'order_id'=> 'required|integer|exists:orders,id'
            ]);

        $order=Order::where('user_id', Auth::id())
                    ->findOrFail($request->order_id);
        
        //Verifico ordine in stato pending
        if ($order->payment_status !== 'pending') {
            return response()->json(['message'=>'Ordine già pagato'], 400);
            }

        //Crea Payment Intent con stripe
        $paymentIntent=PaymentIntent::create([
                'amount'=> (int)($order->total*100), //strime usa centesimi
                'currency'=>'eur',
                'metadata'=>[
                            'order_id'=> $order->id,
                            'user_id'=> Auth::id()
                            ]
                        ]);

        return response()->json([
                'client_secret'=> $paymentIntent->client_secret,
                'payment_intent_id'=> $paymentIntent->id
                ]);
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'order_id'=> 'required|integer|exists:orders,id',
            'payment_intent_id'=> 'required|string'
        ]);

        $order=Order::where('user_id', Auth::id())
                ->findOrFail($request->order_id);
        
        //Recupera payment intent da stripe
        $paymentIntent=PaymentIntent::retrieve($request->payment_intent_id);

        if ($paymentIntent->status==='succeeded') {
            //Aggiorna ordine
            $order->update([
                    'status'=> 'confirmed',
                    'payment_status'=> 'paid'
                ]);
            
        
            //Salva pagamento nel db
            Payment::create([
                'order_id'=> $order->id,
                'stripe_payment_id'=> $paymentIntent->id,
                'amount'=> $order->total,
                'currency'=> 'eur',
                'status'=> 'completed'
                ]);

            return response()->json([
                    'message'=> 'Pagamento completato',
                    'order'=> $order
                    ]);
        }

        return response()->json(['message'=> 'Pagamento fallito'], 400);

    }



}
