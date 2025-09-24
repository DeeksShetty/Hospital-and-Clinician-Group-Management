<?php
namespace App\Services;

use App\Models\Group;

class GroupService
{
    
     //Prevent self parenting.
    
    public function isSelfParent(int $id, ?int $parentId): bool
    {
        return $id === $parentId;
    }

    
     //Check if potentialParentId is a descendant of groupId.
     
    public function isDescendant(int $groupId, int $potentialParentId): bool
    {
        $children = Group::where('parent_id', $groupId)->pluck('id');

        foreach ($children as $childId) {
            if ((int) $childId === $potentialParentId) {
                return true;
            }
            if ($this->isDescendant((int) $childId, $potentialParentId)) {
                return true;
            }
        }
        return false;
    }

    
     // Check if potentialParentId is a descendant of groupId for unit test.
     
    public function isDescendantArray(int $groupId, int $potentialParentId, array $groups): bool
    {
        foreach ($groups as $g) {
            if (($g['parent_id'] ?? null) === $groupId) {
                if (($g['id'] ?? null) === $potentialParentId) {
                    return true;
                }
                if ($this->isDescendantArray($g['id'], $potentialParentId, $groups)) {
                    return true;
                }
            }
        }
        return false;
    }
}
