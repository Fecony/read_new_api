<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PreferenceController extends Controller
{
    public const CACHE_TTL = 600; // 10 minutes

    public function options(): JsonResponse
    {
        $options = cache()->remember('users', self::CACHE_TTL, function () {
            // NOTE: would be much better to store categories, authors in separate models
            $articles = Article::all([
                'category',
                'source',
                'author',
            ]);

            return [
                'sourceOptions' => $articles->unique('source')
                    ->map(fn($article) => [
                        'value' => $article->source,
                        'label' => Str::headline($article->source),
                    ])->values(),

                'categoryOptions' => $articles->whereNotIn('category', [null, ''])
                    ->unique('category')
                    ->map(fn($article) => [
                        'value' => Str::lower($article->category),
                        'label' => Str::headline($article->category),
                    ])->values(),

                'authorOptions' => $articles->whereNotIn('author', [null, ''])
                    ->unique('author')
                    ->map(fn($article) => [
                        'value' => Str::lower($article->author),
                        'label' => $article->author,
                    ])->values(),
            ];
        });

        return response()->json($options);
    }

    public function index(Request $request): JsonResponse
    {
        $preferences = $request->user()->settings()->all();

        return response()->json([
            'preferences' => $preferences
        ], Response::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $settings = $request->all();
        $user = $request->user();

        $user->settings()->apply((array) $settings);

        return response()->json([], Response::HTTP_OK);
    }
}
