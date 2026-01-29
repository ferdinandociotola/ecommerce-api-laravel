<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
       //creaiamo un admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
	    'password'=> bcrypt('password'),
        ]);
    
	//crea 5 users normali
	User::factory(5)->create();

	//crea 5 categorie
	Category::factory(5)->create();

	//crea 20 prodotti
	Product::factory(20)->create();
    }




}
