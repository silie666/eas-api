<?php

Route::get('/{any}', function ($providerName) {
    return response()->json(json_decode(app('api-docs')->generate($providerName), true));
})->where('any', '.*');