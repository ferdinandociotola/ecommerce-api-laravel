<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable=[
		'session_id',
		'user_id',
		'product_id',
		'quantity'
		];
	
	//Relazione cart_item a 1 user
	public function user()
	{
	return $this->belongsTo(User::class);
	}

	//Relazione cart_item appartiene a 1 product
	public function product()
	{
	return $this->belongsTo(Product::class);
	}



}
