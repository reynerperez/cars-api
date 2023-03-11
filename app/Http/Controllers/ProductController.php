<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends controller
{
    public function latest(){
        $products = Product::with('images')->latest()->take(10)->get();
        return response()->json(['products' => $products, 'message' => 'OK'], 200);
    }

    public function featured(){
        $products = Product::with('images')
            ->WhereHas('category', function($q){
                $q->where('name', 'Destacados');
            })
            ->orderBy('updated_at', 'desc')->get();
        return response()->json(['products' => $products, 'message' => 'OK'], 200);
    }

    public function catalog(Request $request){
        $products = Product::with('images')->orderBy('updated_at', 'desc');
        if($request->has('category')){
            $products = $products->where('category_id', $request->query('category') );
        }
        $products = $products->cursorPaginate(6);
        return response()->json($products, 200);
    }

    public function show($id){
        $product = Product::with('images','category')->find($id);
        return response()->json($product, 200);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'images'=>'required|array',
            'images.*'=>'required|mimes:png,jpg,jpeg',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'numeric',
            'description' => 'string',
        ]);
        //return count($request->file('images'));

        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category_id = $request->category;
        $product->save();

        foreach ($request->file('images') as $index => $imagefile) {
            $name = $product->id.time().$index.".".$imagefile->extension();
            $image = new ProductImage;
            $path = $imagefile->storeAs('/images/products', $name, 'public');
            $image->path = $path;
            $image->product_id = $product->id;
            $image->save();
        }

        return response()->json(['product' => $product, 'message' => 'CREATED'], 201);

    }

    public function update($id, Request $request) {

        $this->validate($request, [
            'images'=>'required|array',
            'images.*'=>'required|mimes:png,jpg,jpeg',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'numeric',
            'description' => 'string',
        ]);

        $product = Product::findOrFail($request->route('id'));
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category_id = $request->category;
        $product->save();


        //Delete old Images
        foreach ($product->images as $image){
            if(Storage::disk('public')->exists($image->path)){
                Storage::disk('public')->delete($image->path);
            }
        }
        $product->images()->delete();

        foreach ($request->file('images') as $index => $imagefile) {
            $name = $product->id.time().$index.".".$imagefile->extension();
            $image = new ProductImage;
            $path = $imagefile->storeAs('/images/products', $name, 'public');
            $image->path = $path;
            $image->product_id = $product->id;
            $image->save();
        }

        return response()->json(['product' => $product, 'message' => 'UPDATED'], 200);
    }
}
