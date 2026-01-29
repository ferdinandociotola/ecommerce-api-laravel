<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable=[
		'user_id',
		'total',
		'status',
		'payment_status',
		'shipping_address'
		];

	//Relazione order per 1 user
	public function user()
	{
	return $this->belongsTo(User::class);
	}
	
	 // Relazione: order ha molti order_items
	public function orderItems()
	{
	return $this->hasMany(OrderItem::class);
	}

	//relazione order ha 1 payment
	public function payment()
	{
	return $this->hasOne(Payment::class);
	}


}
