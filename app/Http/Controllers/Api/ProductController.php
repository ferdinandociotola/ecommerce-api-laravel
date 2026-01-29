<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
        public function index()
    {
        $products=Product::with('category')->get();

	return response()->json([
			'success'=>true,
			'data'=>$products
			]);   
   }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Product $product)
    {
        return response()->json([
			'success'=>true,
			'data'=> $product->load('category')
			]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
		'success'=> true,
		'data'=> $product->load('category')
		]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
