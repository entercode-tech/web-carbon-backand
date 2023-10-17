<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

function generateUuid()
{
    $uuid = time() * 1000;
    return $uuid;
}

function uploadImage($image, $path)
{
    Log::info($image);
    try {
        $imageName = time() . '.' . $image->extension();
        $url = Storage::putFileAs('public/images/' . $path, $image, $imageName);
        return $url;
    } catch (\Throwable $th) {
        Log::error($th->getMessage());
    }
}

function deleteImage($pathName)
{
    try {
        Storage::delete($pathName);
    } catch (\Throwable $th) {
        Log::error($th->getMessage());
    }
}
