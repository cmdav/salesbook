<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = [
            'name' => 'Test user',
            'email' => 'user@gmail.com',
            'password' =>'Password',
            'password_confirmation' =>'Password'
        ];

        
       
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('api/v1/users', $this->user);

        $response->assertStatus(201);

        $this->assertCredentials([
            'email' => 'user@gmail.com',
            'password' => 'Password',
        ]);
    }
    public function test_user_can_login(): void
    {

        $user = User::create($this->user);

        $loginData = [
            'email' => 'user@gmail.com',
            'password' => 'Password',
        ];
    
        $response = $this->post('api/v1/login', $loginData);
    
        $response->assertStatus(200);
    }
}
