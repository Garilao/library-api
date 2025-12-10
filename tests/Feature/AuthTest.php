<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_register_and_login()
    {
        $userData = [
            'name'=>'Test User',
            'email'=>'test@example.com',
            'password'=>'password',
            'password_confirmation'=>'password'
        ];

        $this->postJson('/api/register', $userData)->assertStatus(201)->assertJsonStructure(['user','token']);

        $login = $this->postJson('/api/login', ['email'=>'test@example.com','password'=>'password']);
        $login->assertStatus(200)->assertJsonStructure(['user','token']);
    }

    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
}
