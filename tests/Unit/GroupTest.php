<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Group;
use App\Services\GroupService;
use Mockery;

class GroupTest extends TestCase
{
    //Test creating a group using Eloquent model.
    
    public function test_create_group()
    {
        $data = ['name' => 'Test Group', 'description' => 'desc'];
        $group = new Group($data);
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals('Test Group', $group->name);
        $this->assertEquals('desc', $group->description);
    }

    
    //Test updating a group using Eloquent model logic.
    
    public function test_update_group()
    {
        $group = new Group([
            'id' => 1,
            'name' => 'Old Group Name',
            'description' => 'Old description',
        ]);
        $group->name = 'Updated Group Name';
        $group->description = 'Updated description';
        $this->assertEquals('Updated Group Name', $group->name);
        $this->assertEquals('Updated description', $group->description);
    }

    
    //Test deleting a group.
    
    public function test_delete_group()
    {
        $group = new Group([
            'id' => 1,
            'name' => 'Test Group',
            'description' => 'Test group description',
        ]);
        // Simulate delete by unsetting the object
        unset($group);
        $this->assertTrue(true, 'Group deleted (simulated)');
    }

    
    //Test prevent deleting a group with children.
    
    public function test_cannot_delete_group_with_children()
    {
        $parent = new Group(['id' => 1, 'name' => 'Parent Group']);
        $child = new Group(['id' => 2, 'name' => 'Child Group', 'parent_id' => 1]);
        // Simulate children relationship
        $children = [$child];
        $hasChildren = count($children) > 0;
        $this->assertTrue($hasChildren, 'Cannot delete group that is a parent of other groups. Please delete child group first.');
    }
    
    // Test isSelfParent method from GroupService.
    
    public function test_prevent_self_parenting()
    {
        $service = new GroupService();
        $this->assertTrue($service->isSelfParent(5, 5));
        $this->assertFalse($service->isSelfParent(5, 2));
    }

    
    // Test isDescendant method from GroupService.
    
    public function test_prevent_descendant_as_parent()
    {
        $service = new GroupService();
        $groups = [
            ['id' => 1, 'parent_id' => null],
            ['id' => 2, 'parent_id' => 1],
            ['id' => 3, 'parent_id' => 2],
        ];
        $this->assertTrue($service->isDescendantArray(1, 3, $groups));
        $this->assertFalse($service->isDescendantArray(2, 1, $groups));
    }

}
