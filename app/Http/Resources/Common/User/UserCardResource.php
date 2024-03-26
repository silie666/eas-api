<?php

namespace App\Http\Resources\Common\User;

use App\Http\Resources\StudentApi\BaseResource;

class UserCardResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'id'              => static::propInt('卡id'),
            'brand_name'      => static::propString('品牌名称'),
            'number'          => static::propString('卡号'),
            'expiration_date' => static::propDatetime('过期时间'),
            'create_time'     => static::propDatetime('创建时间'),
        ];
    }
}