<?php

namespace App\Http\Resources\API\V1;

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
        // return parent::toArray($request);
        return [
            //'type' => 'users',
            'type' => $this->role,
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'phone'=>$this->phone,
                'address'=>$this->address,
                'birth_date'=>$this->birth_date,
                // 'created_at' => $this->created_at,
                // 'updated_at' => $this->updated_at,
                'token' => $this->when(isset($this->token), $this->token),
            ]
        ];
    }
    public function with(Request $request): array
    {
        return [
            // 'meta' => [
            //     'token' => $this->when(isset($this->token), $this->token),
            // ],
        ];
    }
}
