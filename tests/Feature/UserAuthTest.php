<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function test_user_can_login()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        // Send a POST request to the login route using correct password
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
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
    }

    public function test_user_can_logout()
    {
        // Create a user and login
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        // Generate token for authentication check
        $token = $user->createToken('TestApp')->plainTextToken;

        // Send a POST request to logout route with the token in the Authorization header part
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');

        // Assert that the response is successful and the token was invalidated
        $response->assertStatus(200)
                 ->assertJson([
                    'message' => 'Logged out successfully.',
                    'status' => true, 
                 ]);
    }
}
