<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostcardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $url = $this->file_carbon_path ? asset('storage/' . str_replace('public/', '', $this->file_carbon_path)) : null;
        return [
            'id' => $this->id,
            'uniq_id' => $this->uniq_id,
            'code' => $this->code,
            'guest_id' => $this->guest_id,
            'file_carbon_path' => $url,
            'metric_tons' => $this->metric_tons,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
