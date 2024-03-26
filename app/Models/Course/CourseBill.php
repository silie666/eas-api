<?php

namespace App\Models\Course;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'status',
        'course_ids',
    ];

    protected $casts = [
        'course_ids' => 'array',
    ];


    public function studentCourseBills(){
        return $this->hasMany(StudentCourseBill::class);
    }
    public function getCoursesAttribute()
    {
        return $this->course_ids ? Course::whereIn('id', $this->course_ids)->get()->all() : [];
    }

}