<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;
use Midtrans\Snap;

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

function createMidtransTransaction($order_id, $amount)
{
    try {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED');
        Config::$overrideNotifUrl = env('MIDTRANS_NOTIFICATION_URL');

        $data = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $amount,
            ],
        ];

        $payment = Snap::createTransaction($data)->redirect_url;
        return $payment;
    } catch (\Throwable $th) {
        Log::error($th->getMessage());
    }
}
