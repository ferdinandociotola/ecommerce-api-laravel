<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	use HasFactory; //aggiunge dati fake

    protected $fillable=[
		'name',
		'slug'
		];


	//relazione: 1 category molti products
	public function products()
	{
		return $this->HasMany(Product::class);
	}




}
