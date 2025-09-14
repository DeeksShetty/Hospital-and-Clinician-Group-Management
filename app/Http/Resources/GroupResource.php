<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{

    /**
     * @OA\Schema(
     *     schema="GroupResource",
     *     type="object",
     *     title="Group Resource",
     *     description="Group resource representation",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Developers Team"),
     *     @OA\Property(property="description", type="string", example="A group for developers"),
     *     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-29T12:34:56Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-29T12:34:56Z"),
     *     @OA\Property(property="child_groups", type="array", @OA\Items(ref="#/components/schemas/GroupResource"))
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'description' => $this->description,
            'image' => $this->image,
        ];
    }
}
