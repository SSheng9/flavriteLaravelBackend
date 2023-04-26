<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $image = $this->faker->image($dir = storage_path('app/public'), $width = 640, $height = 480);
        return [
            //
            'name' => $this->faker->name(),
            'nickname' => $this->faker->name(),
            'total_likes' => $this->faker->randomDigit(),
            'thumbnail' => $image,
        ];
    }
}
