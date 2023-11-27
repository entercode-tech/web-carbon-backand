<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostcardTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $url = $this->image_path ? asset('storage/' . $this->image_path) : null;
        return [
            'id' => $this->id,
            'uniq_id' => $this->uniq_id,
            'name' => $this->name,
            'image_path' => $url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
