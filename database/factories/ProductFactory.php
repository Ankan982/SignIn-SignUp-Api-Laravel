<?php

namespace Database\Factories;


use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_name' => $this->faker->text(maxNbChars:10),
            'product_description'=> $this->faker->text(maxNbChars:80),
            'product_price'=> $this->faker->numberBetween(50,100),
            'product_quantity'=> $this->faker->numberBetween(5,10),
        ];
    }
}
