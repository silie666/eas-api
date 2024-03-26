<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User\Student;
use App\Models\User\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

abstract class BaseFactory extends Factory
{
    public function firstStudent(): Student
    {
        return Student::first();
    }

    public function firstTeacher(): Teacher
    {
        return Teacher::first();
    }

}
