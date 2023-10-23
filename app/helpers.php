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

function generateRandomToken()
{
    return bin2hex(random_bytes(32));
}

function uniqCode($existingCodes)
{
    $code = rand(100000, 999999);
    // if code not in existingCodes, return code
    if (!in_array($code, $existingCodes)) {
        return $code;
    } else {
        uniqCode($existingCodes);
    }
}

function generateOrderId()
{
    $orderId = "trx-" . time();
    return $orderId;
}

function metric_value($metric_tons)
{
    return $metric_tons * 100000;
}
