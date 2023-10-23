<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "uniq_id" => $this->uniq_id,
            "order_id" => $this->order_id,
            "guest" => [
                "id" => $this->guest->id,
                "uniq_id" => $this->guest->uniq_id,
                "first_name" => $this->guest->first_name,
                "last_name" => $this->guest->last_name,
                "email" => $this->guest->email,
                "location" => $this->guest->location,
            ],
            "postcard" => [
                "id" => $this->postcard->id,
                "uniq_id" => $this->postcard->uniq_id,
                "code" => $this->postcard->code,
                "file_carbon_path" => $this->postcard->file_carbon_path,
                "metric_tons" => $this->postcard->metric_tons,
            ],
            "amount" => $this->amount,
            "currency" => $this->currency,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
