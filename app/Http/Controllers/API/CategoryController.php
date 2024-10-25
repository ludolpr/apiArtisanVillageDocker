<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
   
    public function index()
    {
        $category = Category::orderBy('name_category', 'asc')->get();
        return response()->json($category);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name_category' => 'required|max:50',
            'description_category' => 'required|max:400',
        ]);

        $category = Category::create([
            'name_category' => $request->name_category,
            'description_category' => $request->description_category
        ]);

        // JSON response
        return response()->json([
            'status' => 'Success',
            'data' => $category,
        ]);
    }

    
    public function show(Category $category)
    {
        return response()->json($category);
    }

   
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name_category' => 'required|max:50',
            'description_category' => 'required|max:400',
        ]);

        $category->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succès",
            "data" => $category
        ]);
    }

    
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}