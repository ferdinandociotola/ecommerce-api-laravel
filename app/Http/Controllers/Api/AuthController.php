<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    
	// POST /api/register
    public function register(Request $request)
    {
	$request->validate([
	'name'=> 'required|string',
	'email'=>'required|email|unique:users',
	'password'=>'required|min:6'
	]);

	$user=User::create([
	'name'=>$request->name,
	'email'=>$request->email,
	'password' => bcrypt($request->password),
    'role' => 'user'
	]);

	$token =$user->createToken('auth-token')->plainTextToken;

	// Login automatico dopo registrazione
    Auth::login($user);

    // MERGE CART: sessione → database
    $this->mergeCart($request);

	return response()->json([
			'success'=> true,
			'user'=> $user,
			'token'=> $token
			], 201);
     }	


	//post /api/login
	public function login(Request $request)
	{
		$request->validate([
			'email'=>'required|email',
			'password'=> 'required'
			]);
		
		//$user=User::where('email', $request->email)->first();

		//if (!$user || !Hash::check($request->password, $user->password)) {
		if (!Auth::attempt($request->only('email', 'password'))){
		return response()->json([
			'message'=> 'Credenziali non valide'
			], 401);
			}

			$user=Auth::user();
	    	$token = $user->createToken('auth-token')->plainTextToken;

			//MergeCart: sessione->database
			$this->mergeCart($request);

    		return response()->json([
        		'success' => true,
        		'user' => $user,
        		'token' => $token
    		]);

	}


	//post /api/logout
	public function logout(Request $request)
	{
		$request->user()->currentAccessToken()->delete();

		return response()->json([
			'success'=> true,
			'message'=> 'Logged out'
			]);
	}

	private function mergeCart(Request $request)
	{

		//Leggi carrello sessione
		$sessionCart=session('cart', []);

		if (empty($sessionCart)) {
			return; //nessun prodotto
		}
	

		$userId=Auth::id();

		foreach($sessionCart as $productId => $data) {
			//Cerca se prodotto è già nel db
			$cartItem=CartItem::where('user_id', $userId)
					->where('product_id', $productId)
					->first();

			if ($cartItem) {
				//Prodotto esiste->somma quantità
				CartItem::create([
				'user_id' => $userId,
				'product_id'=> $productId,
				'quantity'=> $data['quantity']
				]);
			}

		}
		//Cancesso la sessione
		session()->forget('cart');
	}



}
