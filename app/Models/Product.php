<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	use HasFactory; //aggiunge dati fake

	protected $table = 'products'; 

    protected $fillable=[
		'category_id',
		'name',
		'description',
		'price',
		'stock',
		'image_url'
		];

	//relazione:product appartiene a 1 category
	public function category()
	{
	return $this->belongsTo(Category::class);
	}

	//Relazione: product puo essere molti cart_items
	public function cart_items()
	{
	return $this->hasMaby(CartItem::class);
	} 
	
	//Relazione: product puo essere molti order_items
	public function order_items()
	{
	return $this->hasMany(OrderItem::class);
	}

}
