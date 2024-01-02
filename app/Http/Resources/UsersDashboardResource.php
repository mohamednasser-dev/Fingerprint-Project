<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'email' => (string)$this->email,
            'phone' => (string)$this->phone,
            'is_active' => (int)$this->is_active,
            'type' => (string)$this->type,
        ];
    }
}
