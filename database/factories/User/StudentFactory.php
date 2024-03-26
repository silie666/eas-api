<?php

namespace Database\Factories\User;

use App\Models\User\Student;
use Database\Factories\BaseFactory;


class StudentFactory extends BaseFactory
{
    protected $model = Student::class;

    public function definition()
    {
        $faker = $this->faker;
        return [
            'username' => $faker->userName,
            'name'     => $faker->name,
            'email'    => $faker->email,
            'password' => $faker->password,

        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Student $student) {
        });
    }
}