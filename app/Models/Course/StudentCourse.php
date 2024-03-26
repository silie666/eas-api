<?php

namespace App\Models\Course;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_course_bill_id',
        'course_id',
        'student_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}