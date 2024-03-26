<?php

namespace App\Models\User;


use App\Models\Card\Card;
use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $table = 'students';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var string[]
     */
    protected $hidden = ['password'];

    /**
     * 查询用户
     *
     * @param $username
     *
     * @return mixed
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * 关联课程
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student_pivot', 'student_id', 'course_id');
    }

    public function cards()
    {
        return $this->morphMany(Card::class, 'card_table');
    }
}