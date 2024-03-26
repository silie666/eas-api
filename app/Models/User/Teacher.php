<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'admin_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['username', 'password', 'name', 'avatar'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * 查询用户
     *
     * @param $username
     *
     * @return mixed
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->whereHas('roles', function ($query) {
            // todo 这里是查询后台管理相关的教师角色
            return $query->where('slug', 'teacher');
        })->first();
    }

    /**
     * 关联多个角色
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = 'admin_role_users';
        return $this->belongsToMany(Role::class, $pivotTable, 'user_id', 'role_id');
    }
}
