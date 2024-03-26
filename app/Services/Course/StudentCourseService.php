<?php

namespace App\Services\Course;

use App\Models\Course\StudentCourse;
use App\Services\BaseService;
use App\Services\SqlBuildService;

class StudentCourseService extends BaseService
{
    /**
     * 学生课程查询对象
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function query(array $attributes = [])
    {
        $query = StudentCourse::query();

        $query = SqlBuildService::buildEqualQuery($query, $attributes, [
            'student_id' => 'student_id',
            'course_id'  => 'course_id',
        ]);
        $query = SqlBuildService::buildLikeQuery($query, $attributes, [
            'name' => 'course_name',
            'date' => 'course_date',
            'fees' => 'bill_fees',
        ], 'course');

        $query->orderByDesc('id');
        return $query;
    }

    /**
     * 创建学生课程
     *
     * @param array $attributes
     * @param bool  $isProcessed
     *
     * @return \App\Models\Course\StudentCourse
     */
    public static function create(array $attributes, bool $isProcessed = false)
    {
        if (!$isProcessed) {
            $attributes = static::processAttributes($attributes);
        }
        return StudentCourse::create($attributes);
    }

    /**
     * 处理属性
     *
     * @param array $attributes
     *
     * @return array
     */
    public static function processAttributes(array $attributes = [])
    {
        $collect = collect($attributes);

        return $collect->all();
    }
}