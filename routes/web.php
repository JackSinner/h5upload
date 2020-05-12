<?php

use Encore\h5upload\Http\Controllers\h5uploadController;

Route::get('h5upload', h5uploadController::class . '@index');
Route::post('h5upload_info', h5uploadController::class . '@info');
Route::post('saved', h5uploadController::class . '@saved');
Route::get('manage', h5uploadController::class . '@manage');
Route::get('treeinfo', h5uploadController::class . '@treeInfo');
