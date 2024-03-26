<?php

namespace Tests;

use App\Models\User\Student;
use App\Models\User\Teacher;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery\MockInterface;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static bool $isInit;

    protected function setUp(): void
    {
        parent::setUp();

        if (!static::$isInit) {
            static::$isInit = true;
            $this->createTeacher();
            $this->createStudent();
            $this->setUpOnce();
        }
    }

    protected function setUpOnce(): void
    {
    }

    public static function setUpBeforeClass(): void
    {
        static::$isInit = false;
    }

    public function createTeacher()
    {
        if (!Teacher::query()->first()) {
            Teacher::factory()->create();
        }
    }

    public function createStudent()
    {
        if (!Student::query()->first()) {
            Student::factory()->create();
        }
    }

    /**
     * 模拟类
     *
     * @param string $class
     *
     * @return \Mockery\MockInterface
     */
    protected function mockClass(string $class): MockInterface
    {
        return \Mockery::fetchMock($class) ?: \Mockery::mock('overload:' . $class);
    }
}
