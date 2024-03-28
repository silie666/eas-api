<?php

namespace App\Services\Course;

use App\Models\Course\Course;
use App\Models\User\Teacher;
use App\Services\BaseService;
use App\Services\SqlBuildService;
use Package\Exceptions\Client\BadRequestException;

class CourseService extends BaseService
{

    /**
     * 课程查询对象
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function query(array $attributes = [])
    {
        $query = Course::query();
        $query = SqlBuildService::buildLikeQuery($query, $attributes, [
            'name' => 'name',
        ]);
        $query = SqlBuildService::buildLikeQuery($query, $attributes, [
            'name' => 'teacher_name',
        ], 'teacher');

        $query->orderByDesc('id');
        return $query;
    }

    /**
     * 获取课程
     *
     * @param int  $courseId
     * @param bool $throw
     *
     * @return \Illuminate\Database\Eloquent\Builder|\App\Models\Course\Course
     */
    public static function getCourse(int $courseId, bool $throw = true)
    {
        $query = Course::query();
        if ($throw) {
            return $query->findOrFail($courseId);
        }
        return $query->find($courseId);
    }

    /**
     * 创建
     *
     * @param array                    $attributes
     * @param \App\Models\User\Teacher $teacher
     * @param bool                     $isProcessed
     *
     * @return \App\Models\Course\Course
     */
    public static function create(array $attributes, Teacher $teacher, bool $isProcessed = false)
    {
        if (!$isProcessed) {
            $attributes = static::processAttributes($attributes);
        }

        $course = Course::create(array_merge($attributes, [
            'teacher_id' => $teacher->id,
        ]));

        return $course;
    }

    /**
     * 更新
     *
     * @param array                         $attributes
     * @param \App\Models\Course\Course     $course
     * @param \App\Models\User\Teacher|null $teacher
     * @param bool                          $isProcessed
     *
     * @return \App\Models\Course\Course
     */
    public static function update(array $attributes, Course $course, Teacher $teacher = null, bool $isProcessed = false)
    {
        if (!$isProcessed) {
            $attributes = static::processAttributes($attributes, $course);
        }
        $course->update($attributes);

        return $course;
    }

    /**
     * 删除
     *
     * @param \App\Models\Course\Course $course
     *
     * @return void
     */
    public static function delete(Course $course)
    {
        if ($course->studentCourseBills->isNotEmpty()) {
            throw new BadRequestException('已生成账单，无法删除！');
        }
        $course->delete();
    }

    /**
     * 处理属性
     *
     * @param array                          $attributes
     * @param \App\Models\Course\Course|null $course
     *
     * @return array
     */
    public static function processAttributes(array $attributes = [], Course $course = null)
    {
        $collect = collect($attributes);

        if ($course) {
            if ($course->studentCourseBills->isNotEmpty()) {
                throw new BadRequestException('已生成账单，无法更新！');
            }
        }

        return $collect->all();
    }
}