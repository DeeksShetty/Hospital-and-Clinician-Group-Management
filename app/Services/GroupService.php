<?php
namespace App\Services;

use App\Models\Group;

class GroupService
{
    
     //Prevent self parenting.
    
    public function isSelfParent($id,$parentId)
    {
        return $id === $parentId;
    }

    
     //Check if potentialParentId is a descendant of groupId.
     
    public function isDescendant($groupId,$potentialParentId)
    {
        $children = Group::where('parent_id', $groupId)->pluck('id');

        foreach ($children as $childId) {
            if ($childId == $potentialParentId) {
                return true;
            }
            if ($this->isDescendant($childId, $potentialParentId)) {
                return true;
            }
        }
        return false;
    }

    
     // Check if potentialParentId is a descendant of groupId for unit test.
     
    public function isDescendantArray($groupId,$potentialParentId,$groups)
    {
        foreach ($groups as $g) {
            if ($g['parent_id'] === $groupId) {
                if ($g['id'] === $potentialParentId) {
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
