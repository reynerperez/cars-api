<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController
{
    public function index(Request $request){
        $visible = $request->query('visible') != null;
        //DB::enableQueryLog();
        $categories = Category::Query();
        $categories = $visible ? $categories->WithNotVisible() : $categories->Visible();
        $categories = $categories->withCount('products');
        $categories = $categories->orderBy('name')->get();
        //dd(DB::getQueryLog());
        return response()->json($categories);
    }

    public function create(Request $request){
        $category = new Category;
        $category->name = $request->name;
        $category->save();
        return response()->json($category->refresh());
    }
    public function update($id, Request $request){
        $category = Category::WithNotVisible()->findOrFail($id);
        $category->name = $request->name;
        $category->save();
        return response()->json($category->refresh());
    }

    public function delete($id){
        $category = Category::WithNotVisible()->findOrFail($id);
        $category->delete();
        return response()->json($category);
    }

    public function visible($id){
        $category = Category::WithNotVisible()->findOrFail($id);
        $visible =  $category->visible == 1 ? 0 : 1;
        $category->visible = $visible;
        $category->save();
        return response()->json($category);
    }
}
