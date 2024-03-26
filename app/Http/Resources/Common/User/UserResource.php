<?php

namespace App\Http\Resources\Common\User;


use App\Http\Resources\Common\BaseResource;

class UserResource extends BaseResource
{
    /**
     * @return array
     */
    public static function properties(): array
    {
        return [
            'id'          => static::propInt('用戶id'),
            'name'        => static::propString('用户姓名'),
            'create_time' => static::propNullableDatetime('创建时间'),
        ];
    }
}
