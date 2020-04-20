<?php

use Encore\h5upload\Http\Controllers\h5uploadController;

Route::get('h5upload', h5uploadController::class . '@index');
Route::post('h5upload_info', h5uploadController::class . '@info');
