<?php
use Illuminate\Support\Facades\Route;
Route::get('/', fn () => response()->json(['app' => 'Barz API', 'api' => '/api/health']));
