<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Visualizza carrello
    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        $total = $this->calculateTotal($cart);

        return response()->json([
            'cart' => $cart,
            'total' => $total
        ]);
    }

    // Aggiungi prodotto
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::where('id', $request->product_id)->first();

        if (!$product) {
            return response()->json(['message' => 'Prodotto non trovato'], 404);
        }

        // Verifica stock
        if ($product->stock < $request->quantity) {
            return response()->json(['message' => 'Stock insufficiente'], 400);
        }

        if (Auth::check()) {
            // User loggato → DB
            $this->addToDbCart($request->product_id, $request->quantity);
        } else {
            // Guest → Sessione
            $this->addToSessionCart($request->product_id, $request->quantity);
        }

        return response()->json(['message' => 'Prodotto aggiunto']);
    }

    // Aggiorna quantità
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())
                                ->where('id', $id)
                                ->firstOrFail();
            
            // Verifica stock
            if ($cartItem->product->stock < $request->quantity) {
                return response()->json(['message' => 'Stock insufficiente'], 400);
            }

            $cartItem->update(['quantity' => $request->quantity]);
        } else {
            // Guest: aggiorna sessione
            $cart = session('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $request->quantity;
                session(['cart' => $cart]);
            }
        }

        return response()->json(['message' => 'Quantità aggiornata']);
    }

    // Rimuovi prodotto
    public function destroy($id)
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())
                    ->where('id', $id)
                    ->delete();
        } else {
            $cart = session('cart', []);
            unset($cart[$id]);
            session(['cart' => $cart]);
        }

        return response()->json(['message' => 'Prodotto rimosso']);
    }

    // Svuota carrello
    public function clear()
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            session()->forget('cart');
        }

        return response()->json(['message' => 'Carrello svuotato']);
    }

    // === METODI HELPER ===

    private function getCart($request)
    {
        if (Auth::check()) {
            // User loggato: prendi da DB
            return CartItem::with('product')
                           ->where('user_id', Auth::id())
                           ->get()
                           ->map(function($item) {
                               return [
                                   'id' => $item->id,
                                   'product_id' => $item->product_id,
                                   'name' => $item->product->name,
                                   'price' => $item->product->price,
                                   'quantity' => $item->quantity,
                                   'subtotal' => $item->product->price * $item->quantity
                               ];
                           });
        } else {
            // Guest: prendi da sessione
            $cart = session('cart', []);
            $result = [];

            foreach ($cart as $productId => $data) {
                $product = Product::find($productId);
                if ($product) {
                    $result[] = [
                        'id' => $productId,
                        'product_id' => $productId,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $data['quantity'],
                        'subtotal' => $product->price * $data['quantity']
                    ];
                }
            }

            return collect($result);
        }
    }

    private function addToDbCart($productId, $quantity)
    {
        $cartItem = CartItem::where('user_id', Auth::id())
                            ->where('product_id', $productId)
                            ->first();

        if ($cartItem) {
            // Prodotto già presente: somma quantità
            $cartItem->increment('quantity', $quantity);
        } else {
            // Nuovo prodotto
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    private function addToSessionCart($productId, $quantity)
    {
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            // Prodotto già presente: somma quantità
            $cart[$productId]['quantity'] += $quantity;
        } else {
            // Nuovo prodotto
            $cart[$productId] = ['quantity' => $quantity];
        }

        session(['cart' => $cart]);
    }

    private function calculateTotal($cart)
    {
        return $cart->sum('subtotal');
    }
}