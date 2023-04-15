<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PreferenceOptionsController extends Controller
{
    public const CACHE_TTL = 600; // 10 minutes

    public function __invoke(): JsonResponse
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
}
