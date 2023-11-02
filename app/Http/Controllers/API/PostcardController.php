<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\PostcardResource;
use App\Models\Postcard;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\IncludedFile;

class PostcardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $filter = [
            'code' => $request->code,
        ];

        try {
            if (count(array_filter($filter)) == 0) {
                $data = Postcard::orderBy('created_at', 'desc')->get();
            } else {
                $data = Postcard::where(function ($query) use ($filter) {
                    foreach ($filter as $key => $value) {
                        if ($value) {
                            $query->where($key, 'like', '%' . $value . '%');
                        }
                    }
                })->orderBy('created_at', 'desc')->get();
            }

            return response()->json([
                'status' => true,
                'message' => 'Postcards retrieved successfully',
                'data' => PostcardResource::collection($data),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Postcards retrieval failed',
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
                'guest_id' => ['required'],
                'file_carbon' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'metric_tons' => ['required'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $url = uploadImage($request->file_carbon, 'postcards');
            $data = $request->only(['guest_id', 'metric_tons']);
            $data['uniq_id'] = generateUuid();
            $data['code'] = uniqCode(Postcard::pluck('code')->toArray());
            $data['file_carbon_path'] = $url;

            DB::beginTransaction();
            $postcard = Postcard::create($data);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Postcard created successfully',
                'data' => new PostcardResource($postcard),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            deleteImage($url);
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Postcard creation failed',
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

    /**
     * Send postcard to email.
     */
    public function sendEmail(Request $request, string $id)
    {
        try {
            $postcard = Postcard::where('uniq_id', $id)->first();

            if (!$postcard) {
                return response()->json([
                    'status' => false,
                    'message' => 'Postcard not found',
                ], 404);
            }

            $guest = $postcard->guest;
            $content = [
                'username' => $guest->name,
            ];
            $attachment = [$postcard->file_carbon_path];

            if ($request->included_files) {
                $included_file_ids = $request->included_files;
                $included_files = IncludedFile::whereIn('id', $included_file_ids)->get();
                $file_paths = $included_files->pluck('file_path')->toArray();
                $attachment = array_merge($attachment, $file_paths);
            }

            Mail::to($guest->email)->send(new SendEmail('Postcard', 'emails.send-postcard', $content, $attachment));

            return response()->json([
                'status' => true,
                'message' => 'Postcard sent successfully',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Postcard sending failed',
            ], 500);
        }
    }
}
