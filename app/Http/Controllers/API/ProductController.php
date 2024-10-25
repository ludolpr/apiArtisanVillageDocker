<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;





class ProductController extends Controller
{

     
    
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function indexWithTags()
    {
        $productstags = Product::with('tags')->get();

        if ($productstags->isEmpty()) {
            return response()->json([
                'status' => 'Success',
                'message' => 'No products found',
                'data' => [],
            ]);
        }

        return response()->json([
            'status' => 'Success',
            'data' => $productstags,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name_product' => 'required|max:50',
            'picture_product' => 'required|image|max:10000',
            'price' => 'required',
            'description_product' => 'required',
            'id_company' => 'required',
            'id_category' => 'required'
        ]);
       
        $filename = "";
        if ($request->hasFile('picture_product')) {
            $filenameWithExt = $request->file('picture_product')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_product')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_product')->storeAs('public/uploads/products', $filename);
        }

        $product = Product::create([
            'name_product' => $request->name_product,
            'picture_product' => $filename,
            'price' => $request->price,
            'description_product' => $request->description_product,
            'id_company' => $request->id_company,
            'id_category' => $request->id_category,
        ]);

        if ($request->has('tags') && !empty($request->tags)) {
            $tags = explode(',', $request->tags);
            $product->tags()->sync($tags);
        } else {
            $product->tags()->sync([]);
        }

        return response()->json([
            'status' => 'Success',
            'data' => $product,
        ]);
    }


    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function showWithTags($id)
    {
        $product = Product::with('tags')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => 'Success',
            'data' => $product,
        ]);
    }


    
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name_product' => 'required|max:50',
            'picture_product' => 'nullable',
            'price' => 'required',
            'description_product' => 'required',
            'id_company' => 'required',
            'id_category' => 'required',
        ]);

        $filename = $product->picture_product;
        if ($request->hasFile('picture_product')) {
            if ($product->picture_product) {
                Storage::delete('public/uploads/products/' . $product->picture_product);
            }

            $filenameWithExt = $request->file('picture_product')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_product')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_product')->storeAs('public/uploads/products', $filename);
        }

        $product->update([
            'name_product' => $request->name_product,
            'picture_product' => $filename,
            'price' => $request->price,
            'description_product' => $request->description_product,
            'id_company' => $request->id_company,
            'id_category' => $request->id_category,
        ]);

        if ($request->has('tags') && !empty($request->tags)) {
            $tags = explode(',', $request->tags);
            $product->tags()->sync($tags);
        } else {
            $product->tags()->sync([]);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Product updated successfully',
        ]);
    }


    
    public function destroy(Product $product)
    {
        if ($product->picture_product) {
            Storage::delete('public/uploads/products/' . $product->picture_product);
        }

        $product->delete();

        return response()->json(['status' => 'Success',
            'message' => 'Product deleted successfully',
        ]);
    }
}