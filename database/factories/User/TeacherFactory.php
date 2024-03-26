<?php

namespace Database\Factories\User;

use App\Models\User\Teacher;
use Database\Factories\BaseFactory;


class TeacherFactory extends BaseFactory
{
    protected $model = Teacher::class;

    public function definition()
    {
        $faker = $this->faker;
        return [
            'username' => $faker->userName,
            'password' => $faker->password,
            'name'     => $faker->name,
            'avatar'   => $faker->imageUrl,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Teacher $teacher) {
        });
    }
}