<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;


class TagController extends Controller
{

   
    public function index()
    {
        $tags = Tag::orderBy('name_tag', 'asc')->get();
        return response()->json($tags);
    }


   
    public function store(Request $request)
    {
        $request->validate([
            'name_tag' => 'required|max:50',
        ]);

        $tag = Tag::create([
            'name_tag' => $request->name_tag
        ]);

        return response()->json([
            'status' => 'Success',
            'data' => $tag,
        ]);
    }


    
    
    public function show(Tag $tag)
    {
        return response()->json($tag);
    }


     
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name_tag' => 'required|max:50',
        ]);

        $tag->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succès",
            "data" => $tag
        ]);
    }

 
     
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }

    
}