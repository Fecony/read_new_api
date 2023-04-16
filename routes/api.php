<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\PreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', fn(Request $request) => $request->user());

    Route::get('/preferences', [PreferenceController::class, 'index']);
    Route::post('/preferences', [PreferenceController::class, 'store']);
    Route::get('/preference_options', [PreferenceController::class, 'options']);
});

Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);
