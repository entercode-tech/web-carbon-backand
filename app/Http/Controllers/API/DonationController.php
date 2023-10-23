<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Http\Resources\DonationResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Guest;
use App\Models\Postcard;
use Illuminate\Support\Facades\DB;


class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $donations = Donation::all();
            return response()->json([
                'status' => true,
                'message' => 'Donations retrieved successfully',
                'data' => DonationResource::collection($donations),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Donations could not be retrieved',
                'data' => [],
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'guest_id' => 'required',
                'postcard_id' => 'required',
                'currency' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Donation could not be created',
                    'data' => $validator->errors(),
                ], 400);
            }

            $guest = Guest::find($request->guest_id);
            $postcard = Postcard::find($request->postcard_id);

            if (!$guest) {
                return response()->json([
                    'status' => false,
                    'message' => 'Guest not found',
                    'data' => [],
                ], 404);
            }

            if (!$postcard) {
                return response()->json([
                    'status' => false,
                    'message' => 'Postcard not found',
                    'data' => [],
                ], 404);
            }

            DB::beginTransaction();
            $donation = Donation::create([
                'uniq_id' => uniqid(),
                'order_id' => generateOrderId(),
                'guest_id' => $request->guest_id,
                'postcard_id' => $request->postcard_id,
                'amount' => metric_value($postcard->metric_tons),
                'currency' => $request->currency,
                'status' => 'pending',
            ]);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Donation created successfully',
                'data' => new DonationResource($donation),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Donation could not be created',
                'data' => [],
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
