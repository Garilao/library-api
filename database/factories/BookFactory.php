<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'title'     => $this->faker->sentence(3),
            'author'    => $this->faker->name(),
            'isbn'      => $this->faker->unique()->isbn13(),
            'genre'     => $this->faker->randomElement(['Fiction', 'Non-fiction', 'Sci-Fi', 'Romance', 'History']),
            'stock'     => $this->faker->numberBetween(3, 30),
            'available' => $this->faker->numberBetween(0, 30),
        ];
    }
}
