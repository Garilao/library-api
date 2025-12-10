<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
class UserCannotAccessAdminEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_admin_endpoints()
    {
        $user = User::factory()->create(['role' => 'student']);
        $book = Book::factory()->create();

        $this->actingAs($user,'sanctum')
             ->deleteJson("/api/books/{$book->id}")
             ->assertStatus(403);
    }
}
