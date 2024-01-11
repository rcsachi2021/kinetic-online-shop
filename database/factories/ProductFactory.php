<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->name();
        $slug  = Str::slug($title);
        $subcategories = [9,11];
        $arrayRandKeys = array_rand($subcategories);
        $subCats       = $subcategories[$arrayRandKeys];

        $brands = [1,2];
        $brandRandKeys = array_rand($brands);
        $brand       = $brands[$brandRandKeys];

        return [
            'title' =>  $title,
            'slug' => $slug,
            'category_id' => 10,
            'sub_category_id' => $subCats,
            'brand_id' => $brand,
            'price' => rand(10,1000),
            'sku' => rand(1000, 10000),
            'track_qty' => 'Yes',
            'qty' => 10,
            'is_featured' => 'Yes',
            'status' => 1
        ];
    }
}
