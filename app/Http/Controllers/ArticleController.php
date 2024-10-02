<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index($id)
    {
        if(!Article::where('id', $id)->exists()){
            return response()->json([
                'success' => false,
                'message' => 'Data artikel tidak ditemukan',
            ], 404);
        }

        $article = Article::with('writer:id,name')->findOrFail($id);
        return new ArticleResource($article);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Mengambil data input yang dibutuhkan
        $credentials = $request->only('title', 'content', 'category_id');

        // Membuat artikel baru
        $article = Article::create([
            'title'       => $credentials['title'],
            'content'     => $credentials['content'],
            'category_id' => $credentials['category_id'],
            'user_id'     => Auth::id(), // Menggunakan ID user yang sedang login sebagai penulis
            'status'      => 'draft', // Menentukan status default sebagai 'draft'
            'published_at'=> null,
        ]);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah artikel',
            ], 409);
        }

        // Mengembalikan resource artikel yang baru dibuat
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah artikel',
            'article' => new ArticleResource($article),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Mengambil data input yang dibutuhkan
        $credentials = $request->only('title', 'content');

        // Mengambil artikel yang akan diupdate
        $article = Article::findOrFail($id);

        // Memastikan artikel yang akan diupdate adalah milik user yang sedang login
        if ($article->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengupdate artikel ini',
            ], 403);
        }

        // Melakukan update artikel
        $article->title = $credentials['title'];
        $article->content = $credentials['content'];
        $article->save();

        // Mengembalikan resource artikel yang telah diupdate
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengupdate artikel',
            'article' => new ArticleResource($article),
        ], 200);
    }

    public function destroy(Request $request, $id){
        if(!Article::where('id', $id)->exists()){
            return response()->json([
                'success' => false,
                'message' => 'Data artikel tidak ditemukan',
            ], 404);
        }

        $article = Article::findOrFail($id);

        if ($article->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus artikel ini',
            ], 403);
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus artikel',
        ], 200);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $q = $request->input('q');

        $articles = Article::where('title', 'like', "%$q%")
            ->orWhere('content', 'like', "%$q%")
            ->get();

        if ($articles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data artikel tidak ditemukan',
            ], 404);
        }

        return ArticleResource::collection($articles);
    }
}
