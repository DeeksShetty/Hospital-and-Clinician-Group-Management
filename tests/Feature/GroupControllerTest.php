<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Http\Request;

class GroupControllerTest extends TestCase
{
    private function userLogin(){
        $response = $this->postJson('/api/login', [
            'email' => 'admin@eg.com',//using a base user created using seeder
            'password' => 'password',
        ]);

        // Assert the response contains a token
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'token'
                    ]
                 ]);
        $token = $response->json('data.token');
        return $token;
    }

    private function createGroup($prefix='First',$parentId=null){
        
        //create a first group
        $group = Group::factory()->create([
            'name' => $prefix.' Group',
            'parent_id'=>$parentId,
            'description'=> $prefix.' group description',
        ]);
        return $group;
    }
    
    public function test_can_create_group()
    {
        $token = $this->userLogin();

        $payload = [
            'name' => 'Test Group',
            'parent_id' => null,
            'description' => 'This is a test group',
        ];

        $response = $this->postJson('/api/groups', $payload, [
            'Authorization' => 'Bearer ' . $token  // Add the Bearer token
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => true,
                    'message' => 'Group created successfully.',
                    'data' => [
                        'name' => 'Test Group',
                        'description'=> 'This is a test group'
                    ],
                ]);

        $this->assertDatabaseHas('groups', [
            'name' => 'Test Group',
        ]);
    }

    public function test_can_create_group_with_parent()
    {
        $token = $this->userLogin();
        
        //create a first group
        $group = $this->createGroup('Parent');

        $payload = [
            'name' => 'Child Group',
            'parent_id' => $group->id,
            'description' => 'This is a child group',
        ];

        $response = $this->postJson('/api/groups', $payload, [
            'Authorization' => 'Bearer ' . $token  // Add the Bearer token
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => true,
                    'message' => 'Group created successfully.',
                    'data' => [
                        'name' => 'Child Group',
                        'parent_id' => $group->id,
                        'description'=> 'This is a child group'
                    ],
                ]);

        $this->assertDatabaseHas('groups', [
            'name' => 'Child Group',
            'parent_id' => $group->id,
        ]);
    }

    public function test_cannot_delete_group_with_children()
    {
        $token = $this->userLogin();

        // Create groups
        $parentGroup = $this->createGroup('Parent');
        $childGroup = $this->createGroup('Child',$parentGroup->id);

        $response = $this->deleteJson("/api/groups/{$parentGroup->id}",[],[
            'Authorization' => 'Bearer ' . $token  // Add the Bearer token
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Cannot delete group that is a parent of other groups. Please delete child group first.'
                 ]);
        $this->assertDatabaseHas('groups', ['id' => $parentGroup->id]);
    }

    public function test_can_update_group()
    {
        $token = $this->userLogin();
        
        //create a first group
        $group = $this->createGroup('Original');

        // Update the group
        $updateData = [
            'name' => 'Updated Group Name',
            'parent_id' => null,
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/groups/{$group->id}", $updateData,[
            'Authorization' => 'Bearer ' . $token  // Add the Bearer token
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'Group updated successfully.',
                    'data' => [
                        'name' => 'Updated Group Name',
                        'parent_id' => null,
                        'description' => 'Updated description',
                    ],
                ]);

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Updated Group Name',
        ]);
    }

    public function test_can_get_group_detail()
    {

        $token = $this->userLogin();

        //create a first group
        $parentGroup = $this->createGroup('Parent 2');

        $group = $this->createGroup('child 2',$parentGroup->id);

        $response = $this->getJson('/api/groups/' . $group->id,[
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status', 
                    'message',
                    'data' => [
                        'id', 
                        'parent_id',
                        'name',
                        'image',
                        'description'
                    ]
                ])
                ->assertJson([
                    'status' => true,
                    'message' => 'Group detail.',
                    'data' => [
                        'id' => $group->id,
                        'name' => 'child 2 Group',
                        'parent_id' => $parentGroup->id,
                        'description' => 'child 2 group description',
                    ]
                ]);
    }


    public function test_can_get_group_list_with_tree_structure()
    {
        $token = $this->userLogin();

        // Create groups
        $parent_1 = $this->createGroup('List Parent 1');
        $p1_child_1 = $this->createGroup('L1 child 1',$parent_1->id);
        $p1_c1_child_1 = $this->createGroup('L1 C1 child 1',$p1_child_1->id);
        $p1_child_2 = $this->createGroup('L1 child 2',$parent_1->id);
        $parent_2 = $this->createGroup('List Parent 2');
        $p2_child_1 = $this->createGroup('L2 child 1',$parent_2->id);
        $parent_3 = $this->createGroup('List Parent 3');

        // Call API
        $response = $this->getJson('/api/groups', [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Assert structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'parent_id',
                        'name',
                        'image',
                        'description',
                        'child_groups',
                    ]
                ]
            ])
            ->assertJson([
                'status' => true,
                'message' => 'Group list with full tree structure.',
            ]);

        // Check database has these groups
        $this->assertDatabaseHas('groups', ['name' => 'List Parent 1 Group']);
        $this->assertDatabaseHas('groups', ['name' => 'L1 child 1 Group']);
        $this->assertDatabaseHas('groups', ['name' => 'L1 C1 child 1 Group']);
        $this->assertDatabaseHas('groups', ['name' => 'L1 child 2 Group']);
        $this->assertDatabaseHas('groups', ['name' => 'List Parent 2 Group']);
        $this->assertDatabaseHas('groups', ['name' => 'L2 child 1 Group']);
        $this->assertDatabaseHas('groups', ['name' => 'List Parent 3 Group']);
    }

    public function test_can_delete_group()
    {
        $token = $this->userLogin();

        // Create a group to delete
        $group = $this->createGroup('Delete');

        // Call DELETE API
        $response = $this->deleteJson("/api/groups/{$group->id}", [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Group deleted successfully.',
                'data' => null,
            ]);

        // Check group is actually deleted from database
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }

}
