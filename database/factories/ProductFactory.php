<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name_product' => $this->faker->words(3, true),
            'picture_product' => 'example.jpg',
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description_product' => $this->faker->sentence(10),
            'id_category' => 1,
            'id_company' => 1,
        ];
    }
}
