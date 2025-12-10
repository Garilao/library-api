<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
class AdminCanDeleteBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_book()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $book = Book::factory()->create();

        $this->actingAs($admin, 'sanctum')
             ->deleteJson("/api/books/{$book->id}")
             ->assertStatus(200);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
