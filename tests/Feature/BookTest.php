<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class BookTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_create_book_and_user_cannot()
    {
        $admin = User::factory()->create(['role'=>'admin']);
        $user = User::factory()->create(['role'=>'student']);

        $book = [
            'title'=>'Sample Book',
            'author'=>'Author',
            'stock'=>3
        ];

        $this->actingAs($admin,'sanctum')->postJson('/api/books', $book)->assertStatus(201);
        $this->actingAs($user,'sanctum')->postJson('/api/books', $book)->assertStatus(403);
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
