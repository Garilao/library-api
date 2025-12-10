<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;

class UserCanViewBooksTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_books()
    {
        $user = User::factory()->create();
        Book::factory()->count(5)->create();

        $response = $this->actingAs($user,'sanctum')->getJson('/api/books');
        $response->assertStatus(200)->assertJsonCount(5);
    }
}
