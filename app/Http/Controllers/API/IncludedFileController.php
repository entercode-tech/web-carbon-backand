<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\IncludedFileResource;
use App\Models\IncludedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IncludedFileController extends Controller
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
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Included Files retrieved successfully',
                'data' => IncludedFileResource::collection(IncludedFile::all()),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Included Files retrieval failed',
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
                'name' => ['required'],
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $url = uploadImage($request->image, 'included_files');

            $data = $request->only(['name']);
            $data['image_path'] = $url;
            $data['uniq_id'] = generateUuid();

            DB::beginTransaction();
            $includedFile = IncludedFile::create($data);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Included file created successfully',
                'data' => new IncludedFileResource($includedFile),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            deleteImage($url);
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Included file creation failed',
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
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required'],
                'image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $includedFile = IncludedFile::where('id', $id)->first();

            if (!$includedFile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Included file not found',
                ], 404);
            }

            $data = $request->only(['name']);

            if ($request->image) {
                $url = uploadImage($request->image, 'included_files');
                $data['image_path'] = $url;
                $old_image_path = $includedFile->image_path;
            }

            DB::beginTransaction();
            $includedFile->update($data);
            DB::commit();

            if ($request->image) deleteImage($old_image_path);

            return response()->json([
                'status' => 'success',
                'message' => 'Included file updated successfully',
                'data' => new IncludedFileResource($includedFile),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->image) deleteImage($url);
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Included file update failed',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $includedFile = IncludedFile::where('id', $id)->first();
        try {

            if (!$includedFile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Included file not found',
                ], 404);
            }

            DB::beginTransaction();
            $includedFile->delete();
            DB::commit();

            deleteImage($includedFile->image_path);

            return response()->json([
                'status' => 'success',
                'message' => 'Included file deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Included file deletion failed',
            ], 500);
        }
    }
}
