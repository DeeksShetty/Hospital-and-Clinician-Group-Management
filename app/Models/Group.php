<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Group",
 *   type="object",
 *   required={"name"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *   @OA\Property(property="name", type="string", example="Test Group"),
 *   @OA\Property(property="image", type="string", nullable=true, example="group.png"),
 *   @OA\Property(property="description", type="string", example="This is a group")
 * )
 */
class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'parent_id',
        'image',
        'description',
    ];
}
