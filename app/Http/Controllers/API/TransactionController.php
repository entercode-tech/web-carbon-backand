<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Donation;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = TransactionResource::collection(Transaction::all());
            return response()->json([
                'status' => true,
                'message' => 'Transactions retrieved successfully',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Transactions retrieval failed',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->transaction_status === "settlement" && $request->status_message === "midtrans payment notification") {
            try {
                $order_id = $request->order_id;
                $donation = Donation::where('order_id', $order_id)->first();

                if (!$donation) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Transaction creation failed',
                    ], 500);
                }

                $guest = $donation->guest;

                $data = [
                    'uniq_id' => generateUuid(),
                    'order_id' => $order_id,
                    'guest_name' => $guest->first_name . ' ' . $guest->last_name,
                    'amount' => $donation->amount,
                ];

                DB::beginTransaction();
                $donation->status = 'success';
                $donation->save();
                $transaction = Transaction::create($data);
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Transaction created successfully',
                    'data' => new TransactionResource($transaction),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction creation failed',
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Transaction creation failed',
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
