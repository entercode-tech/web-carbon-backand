<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncludedFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $url = $this->file_path ? asset('storage/' . str_replace('public/', '', $this->file_path)) : null;
        return [
            'id' => $this->id,
            'uniq_id' => $this->uniq_id,
            'name' => $this->name,
            'file_path' => $url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
