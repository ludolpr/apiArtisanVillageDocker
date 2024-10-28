<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'name_company' => $this->faker->company,
            'description_company' => $this->faker->paragraph,
            'picture_company' => $this->faker->imageUrl,
            'zipcode' => $this->faker->postcode,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'siret' => $this->faker->numerify('##########'),
            'town' => $this->faker->city,
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
            'id_user' => User::factory(),
        ];
    }
}
