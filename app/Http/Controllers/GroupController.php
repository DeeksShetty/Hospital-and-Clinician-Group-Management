<?php
namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Services\GroupService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *   name="Groups",
 *   description="Group management APIs"
 * )
 */

/**
 * @OA\Schema(
 *   schema="GroupTree",
 *   type="object",
 *   allOf={@OA\Schema(ref="#/components/schemas/Group")},
 *   @OA\Property(
 *     property="child_groups",
 *     type="array",
 *     description="List of child groups",
 *     @OA\Items(ref="#/components/schemas/GroupTree"),
 *     example={}
 *   )
 * )
 */
class GroupController extends Controller
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

     /**
    * @OA\Post(
    *   path="/api/groups",
    *   tags={"Groups"},
    *   summary="Create a new group",
    *   security={{"sanctum":{}}},
    *   @OA\RequestBody(
    *     required=true,
    *     @OA\JsonContent(ref="#/components/schemas/Group")
    *   ),
    *   @OA\Response(
    *     response=201,
    *     description="Group created successfully",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="status", type="boolean", example=true),
    *       @OA\Property(property="message", type="string", example="Group created successfully."),
    *       @OA\Property(property="data", ref="#/components/schemas/Group")
    *     )
    *   ),
    *   @OA\Response(
    *     response=400,
    *     description="Validation or business error",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="errors", type="boolean", example=true),
    *       @OA\Property(property="status", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Group can't be parent of self"),
    *       @OA\Property(property="data", type="integer", example=5)
    *     )
    *   )
    * )
     */
    public function createGroup(CreateGroupRequest $request){
        try{
            $group = Group::create($request->validated());
            return $this->successResponse(201,'Group created successfully.',new GroupResource($group));
        }catch(Exception $e){
            return $this->errorResponse(400,'Something went wrong',$e->getMessage());
        }
    }


    /**
    * @OA\Put(
    *   path="/api/groups/{id}",
    *   tags={"Groups"},
    *   summary="Update a group",
    *   security={{"sanctum":{}}},
    *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
    *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Group")),
    *   @OA\Response(
    *     response=200,
    *     description="Group updated successfully",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="status", type="boolean", example=true),
    *       @OA\Property(property="message", type="string", example="Group updated successfully."),
    *       @OA\Property(property="data", ref="#/components/schemas/Group")
    *     )
    *   ),
    *   @OA\Response(
    *     response=400,
    *     description="Invalid parent or validation error",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="errors", type="boolean", example=true),
    *       @OA\Property(property="status", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Group can't be parent of self"),
    *       @OA\Property(property="data", type="integer", example=5)
    *     )
    *   ),
    *   @OA\Response(
    *     response=404,
    *     description="Group not found",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="errors", type="boolean", example=true),
    *       @OA\Property(property="status", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Group not found"),
    *       @OA\Property(property="data", type="integer", example=5)
    *     )
    *   )
    * )
     */
    public function updateGroup(CreateGroupRequest $request,$id){
        try{
            $group = Group::findOrFail($id);
            $newParentId = $request->parent_id;
            if($this->groupService->isSelfParent($id, $newParentId)){
                return $this->errorResponse(400,"Group can't be parent of self",$id);
            }
            if ($newParentId && $this->groupService->isDescendant($id, $newParentId)) {
                return $this->errorResponse(400, "Invalid parent, group can't be set as a child of its own descendant.", $newParentId);
            }
            $group->update($request->validated());
            return $this->successResponse(200,'Group updated successfully.',new GroupResource($group));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(404,'Group not found',$id);
        }catch(Exception $e){
            return $this->errorResponse(400,'Something went wrong',$e->getMessage());
        }
    }

    /**
    * @OA\Get(
    *   path="/api/groups",
    *   tags={"Groups"},
    *   summary="Get list of groups with tree structure",
    *   security={{"sanctum":{}}},
    *   @OA\Response(
    *     response=200,
    *     description="Group list with full tree structure.",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="status", type="boolean", example=true),
    *       @OA\Property(property="message", type="string", example="Group list with full tree structure."),
    *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/GroupTree"))
    *     )
    *   ),
    *   @OA\Response(
    *     response=400,
    *     description="Something went wrong",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="errors", type="boolean", example=true),
    *       @OA\Property(property="status", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Something went wrong"),
    *       @OA\Property(property="data", type="string", nullable=true, example=null)
    *     )
    *   )
    * )
    **/
    public function getGroupList(Request $request){
        try{
            $groupList = $this->groupListRecurse();
            return $this->successResponse(200,'Group list with full tree structure.',$groupList);
        }catch(Exception $e){
            return $this->errorResponse(400,'Something went wrong',$e->getMessage());
        }
    }

    private function groupListRecurse($parentId = null){
        $groups = Group::select('id','parent_id','name','image','description')->where('parent_id',$parentId)->get();
        foreach($groups as $group){
            $group->child_groups = $this->groupListRecurse($group->id);
        }
        return $groups;
    }

     /**
    * @OA\Get(
    *   path="/api/groups/{id}",
    *   tags={"Groups"},
    *   summary="Get group detail",
    *   security={{"sanctum":{}}},
    *   @OA\Parameter(
    *     name="id",
    *     in="path",
    *     required=true,
    *     @OA\Schema(type="integer", example=1)
    *   ),
    *   @OA\Response(
    *     response=200,
    *     description="Group detail.",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="status", type="boolean", example=true),
    *       @OA\Property(property="message", type="string", example="Group detail."),
    *       @OA\Property(property="data", ref="#/components/schemas/Group")
    *     )
    *   ),
    *   @OA\Response(
    *     response=404,
    *     description="Group not found",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="errors", type="boolean", example=true),
    *       @OA\Property(property="status", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Group not found"),
    *       @OA\Property(property="data", type="integer", example=5)
    *     )
    *   )
    * )
     */
    public function getGroupDetail($id){
        try{
            $group = Group::findOrFail($id);
            return $this->successResponse(200,'Group detail.',new GroupResource($group));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(404,'Group not found',$id);
        }catch(Exception $e){
            return $this->errorResponse(400,'Something went wrong',$e->getMessage());
        }
    }

    /**
    * @OA\Delete(
    *   path="/api/groups/{id}",
    *   tags={"Groups"},
    *   summary="Delete a group",
    *   security={{"sanctum":{}}},
    *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
    *   @OA\Response(
    *     response=200,
    *     description="Group deleted successfully.",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="status", type="boolean", example=true),
    *       @OA\Property(property="message", type="string", example="Group deleted successfully."),
    *       @OA\Property(property="data", type="string", nullable=true, example=null)
    *     )
    *   ),
    *   @OA\Response(
    *     response=400,
    *     description="Cannot delete parent group",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="errors", type="boolean", example=true),
    *       @OA\Property(property="status", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Cannot delete group that is a parent of other groups. Please delete child group first."),
    *       @OA\Property(property="data", type="integer", example=5)
    *     )
    *   ),
    *   @OA\Response(
    *     response=404,
    *     description="Group not found",
    *     @OA\JsonContent(
    *       type="object",
    *       @OA\Property(property="errors", type="boolean", example=true),
    *       @OA\Property(property="status", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Group not found"),
    *       @OA\Property(property="data", type="integer", example=5)
    *     )
    *   )
    * )
     */
    public function deleteGroup($id){
        try{
            $isParentOfAny = Group::where('parent_id', $id)->exists();
            if($isParentOfAny){
                return $this->errorResponse(400,'Cannot delete group that is a parent of other groups. Please delete child group first.',$id);
            }
            $group = Group::findOrFail($id);
            $group->delete();
            return $this->successResponse(200,'Group deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(404,'Group not found',$id);
        }catch(Exception $e){
            return $this->errorResponse(400,'Something went wrong',$e->getMessage());
        }
    }
}
