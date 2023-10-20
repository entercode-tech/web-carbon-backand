<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\GuestResource;
use App\Models\Guest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = GuestResource::collection(Guest::all());
            return response()->json([
                'status' => 'success',
                'message' => 'Guests retrieved successfully',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Guests retrieval failed',
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
                'first_name' => ['required'],
                'last_name' => ['required'],
                'location' => ['required'],
                'email' => ['email'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $request->only(['first_name', 'last_name', 'location', 'email']);
            $data['uniq_id'] = generateUuid();

            DB::beginTransaction();
            $guest = Guest::create($data);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Guest created successfully',
                'data' => new GuestResource($guest),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Guest creation failed',
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
        try {
            $guest = Guest::where('id', $id)->first();

            if (!$guest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Guest not found',
                ], 404);
            }

            DB::beginTransaction();
            $guest->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Guest deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Guest deletion failed',
            ], 500);
        }
    }
}
