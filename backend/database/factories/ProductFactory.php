<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected static ?array $cachedImages = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (is_null(self::$cachedImages)) {
            $files = glob(public_path('img/dummy/*.png'));
            self::$cachedImages = array_map('basename', $files);
        }

        $randomImage = !empty(self::$cachedImages)
            ? Arr::random(self::$cachedImages)
            : 'default.jpg';

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->text(),
            'price' => fake()->numberBetween(100, 1000000),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'image_url' => env('APP_URL') . '/img/dummy/' . $randomImage,
        ];
    }
}
