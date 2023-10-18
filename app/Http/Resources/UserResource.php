<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // map roles only id and name
        $roles = $this->roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
            ];
        });
        return [
            'id' => $this->id,
            'uniq_id' => $this->uniq_id,
            'name' => $this->name,
            'email' => $this->email,
            'roleId' => $roles[0]['id'],
            'roleName' => $roles[0]['name'],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
