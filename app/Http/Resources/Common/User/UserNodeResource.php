<?php

namespace App\Http\Resources\Common\User;


use App\Http\Resources\StudentApi\BaseResource;

class UserNodeResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'uri'  => static::propString('地址'),
            'sign' => static::propString('节点标志'),
        ];
    }
}
