<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserCannotBorrowIfStockZeroTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_borrow_if_stock_is_zero()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 0, 'available' => 0]);

        $response = $this->actingAs($user,'sanctum')->postJson('/api/borrow', [
            'book_id' => $book->id
        ]);

        $response->assertStatus(400);
    }
}
