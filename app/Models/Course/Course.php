<?php

namespace App\Models\Course;

use App\Models\Model;
use App\Models\User\Student;
use App\Models\User\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'date',
        'fees',
        'content',
        'student_ids',
    ];

    protected $casts = [
        'date'        => 'date',
        'student_ids' => 'array',
    ];

    public function studentCourseBills()
    {
        return $this->hasMany(StudentCourseBill::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function courses()
    {
        return $this->hasMany(StudentCourse::class);
    }

    public function getStudentsAttribute()
    {
        return $this->student_ids ? Student::whereIn('id', $this->student_ids)->get()->all() : [];
    }

}