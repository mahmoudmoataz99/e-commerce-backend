<?php

use Illuminate\Support\Facades\Route;

// this app is api only, so we dont really need web routes
// just leaving a little welcome page so visiting the url in a browser
// doesnt show an error
Route::get('/', function () {
    return response()->json([
        'message' => 'MyShop API is running. See routes/api.php for endpoints.',
    ]);
});
