<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostcardTemplateResource;
use App\Models\PostcardTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PostcardTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Postcard Templates retrieved successfully',
                'data' => PostcardTemplateResource::collection(PostcardTemplate::all()),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Postcard Templates retrieval failed',
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
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $url = uploadImage($request->image, 'templates');

            $data = $request->only(['name']);
            $data['image_path'] = $url;
            $data['uniq_id'] = generateUuid();

            DB::beginTransaction();
            $postcardTemplate = PostcardTemplate::create($data);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Postcard Template created successfully',
                'data' => new PostcardTemplateResource($postcardTemplate),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            deleteImage($url);
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Postcard Template creation failed',
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
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $postcardTemplate = PostcardTemplate::where('id', $id)->first();

            if (!$postcardTemplate) {
                return response()->json([
                    'status' => false,
                    'message' => 'Postcard Template not found',
                ], 404);
            }

            $data = $request->only(['name']);

            if ($request->image) {
                $url = uploadImage($request->image, 'templates');
                $data['image_path'] = $url;
                $old_image_path = $postcardTemplate->image_path;
            }

            DB::beginTransaction();
            $postcardTemplate->update($data);
            DB::commit();

            if ($request->image) deleteImage($old_image_path);

            return response()->json([
                'status' => true,
                'message' => 'Postcard Template updated successfully',
                'data' => new PostcardTemplateResource($postcardTemplate),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->image) deleteImage($url);
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Postcard Template update failed',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $postcardTemplate = PostcardTemplate::where('id', $id)->first();
        try {

            if (!$postcardTemplate) {
                return response()->json([
                    'status' => false,
                    'message' => 'Postcard Template not found',
                ], 404);
            }

            DB::beginTransaction();
            $postcardTemplate->delete();
            DB::commit();

            deleteImage($postcardTemplate->image_path);

            return response()->json([
                'status' => true,
                'message' => 'Postcard Template deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Postcard Template deletion failed',
            ], 500);
        }
    }
}
