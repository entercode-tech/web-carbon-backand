<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostcardTemplateResource;
use App\Models\PostcardTemplate;
use Illuminate\Support\Facades\Validator;

class PostcardTemplateController extends Controller
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
            'message' => 'Postcard Templates retrieved successfully',
            'data' => PostcardTemplateResource::collection(PostcardTemplate::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        $imageName = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/images/templates', $imageName);
        $url = asset('storage/images/templates/' . $imageName);

        $data = $request->only(['name']);
        $data['image_path'] = $url;
        $data['uniq_id'] = generateUuid();

        $postcardTemplate = PostcardTemplate::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Postcard Template created successfully',
            'data' => new PostcardTemplateResource($postcardTemplate),
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
        $validator = Validator::make($request->all(), [
            'name' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $postcardTemplate = PostcardTemplate::where('id', $id)->first();

        if (!$postcardTemplate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Postcard Template not found',
            ], 404);
        }

        $data = $request->only(['name']);
        $postcardTemplate->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Postcard Template updated successfully',
            'data' => new PostcardTemplateResource($postcardTemplate),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $postcardTemplate = PostcardTemplate::where('id', $id)->first();

        if (!$postcardTemplate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Postcard Template not found',
            ], 404);
        }

        $postcardTemplate->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Postcard Template deleted successfully',
        ]);
    }
}
