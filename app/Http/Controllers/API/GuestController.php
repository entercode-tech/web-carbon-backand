<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\GuestResource;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Guests retrieved successfully',
            'data' => GuestResource::collection(Guest::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        $guest = Guest::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Guest created successfully',
            'data' => new GuestResource($guest),
        ]);
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
        // delete guest
        $guest = Guest::where('id', $id)->first();

        if (!$guest) {
            return response()->json([
                'status' => 'error',
                'message' => 'Guest not found',
            ], 404);
        }

        $guest->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Guest deleted successfully',
        ]);
    }
}
