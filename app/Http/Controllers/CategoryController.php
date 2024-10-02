<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('category_name');

        $category = Category::create([
            'category_name' => $credentials['category_name'],
        ]);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah kategori',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data'    => new CategoryResource($category),
        ], 201);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if (!$category->delete()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus',
        ], 200);
    }
}
