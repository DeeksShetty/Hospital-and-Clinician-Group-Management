<?php
namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller

{

    /**
     * @OA\Post(
     *   path="/api/login",
     *   tags={"Auth"},
     *   summary="User login",
     *   description="Authenticate a user with email and password and receive a Sanctum token.",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="admin@eg.com"),
     *       @OA\Property(property="password", type="string", format="password", example="password")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Login successful",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Login successful"),
     *       @OA\Property(property="data", type="object",
     *         @OA\Property(property="user", ref="#/components/schemas/User"),
     *         @OA\Property(property="token", type="string", example="1|SanctumToken123456")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Invalid credentials",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="errors", type="boolean", example=true),
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Invalid login credentials"),
     *       @OA\Property(property="data", type="string", nullable=true, example=null)
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
     */
    public function login(Request $request){
        try{

            //validating the request
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);
    
            //checking is email exists
            $user = User::where('email',$credentials['email'])->first();
    
            //checking is password match and return message if email or password not matches
            if(! $user || ! Hash::check($credentials['password'],$user->password)){
                return response()->json([
                    'message' => 'Invalid login credentials',
                ], 401);
            }
    
            //deleting old token
            $user->tokens()->delete();
    
            //creating new token
            $token = $user->createToken('api_token')->plainTextToken;
    
            return $this->successResponse(200,'Login successful',[
                'user'    => $user,
                'token'   => $token,
            ]);
        }catch(Exception $e){
            return $this->errorResponse(400,'Something went wrong',$e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *   path="/api/logout",
     *   tags={"Auth"},
     *   summary="User logout",
     *   description="Revoke the current Sanctum token.",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Logged out successfully.",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Logged out successfully."),
     *       @OA\Property(property="data", type="string", nullable=true, example=null)
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="errors", type="boolean", example=true),
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Unauthenticated."),
     *       @OA\Property(property="data", type="string", nullable=true, example=null)
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
     */
    public function logout(Request $request)
    {
        try{
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(200,'Logged out successfully.');
        }catch(Exception $e){
            return $this->errorResponse(400,'Something went wrong',$e->getMessage());
        }
    }
}
