<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;

class BorrowTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
    use RefreshDatabase;
    public function test_user_can_borrow_and_stock_decreases()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock'=>1,'available'=>1]);

        $response = $this->actingAs($user,'sanctum')->postJson('/api/borrow', ['book_id'=>$book->id]);
        $response->assertStatus(201);

        $this->assertDatabaseHas('borrow_records', ['user_id'=>$user->id,'book_id'=>$book->id]);
        $this->assertDatabaseHas('books', ['id'=>$book->id,'available'=>0]);
    }

}
