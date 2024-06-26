<?php

namespace Tests\Unit\Service\Course;

use App\Models\Course\Course;
use App\Models\Course\CourseBill;
use App\Services\Course\CourseBillService;
use App\Services\Course\CourseService;
use Package\Exceptions\Client\BadRequestException;
use Tests\TestCase;

class CourseTest extends TestCase
{
    protected static $course;
    protected static $courseBill;

    protected function setUpOnce(): void
    {
        parent::setUpOnce(); // TODO: Change the autogenerated stub

        static::$course     = Course::factory()->create();
        static::$courseBill = CourseBill::factory()->withCourse(static::$course)->create();
    }

    /**
     * 测试更新
     *
     * @return void
     */
    public function testUpdate()
    {
        static::$course = CourseService::update([
            'name' => 'test',
        ], static::$course);
        $this->assertEquals('test', static::$course->name);
    }

    /**
     * 测试更新异常
     *
     * @depends testUpdate
     */
    public function testUpdateException()
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessageMatches('%无法更新%');
        static::$courseBill = CourseBillService::sendBill(static::$courseBill);
        $this->assertEquals(cons('common.is.yes'), static::$courseBill->status);
        static::$course->refresh();
        CourseService::update([
            'name' => 'test',
        ], static::$course);
    }

    /**
     * 测试删除异常
     *
     * @depends testUpdate
     */
    public function testDeleteException()
    {
        static::$course->refresh();
        $this->expectException(BadRequestException::class);
        CourseService::delete(static::$course);
    }
}