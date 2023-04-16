<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public const PER_PAGE = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $articles = Article::orderBy('id')->paginate(self::PER_PAGE);
        return response()->json($articles);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        //
    }
}
