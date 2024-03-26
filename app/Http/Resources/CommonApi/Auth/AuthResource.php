<?php

namespace App\Http\Resources\CommonApi\Auth;

use App\Http\Resources\CommonApi\BaseResource;

class AuthResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'token_type'   => static::propString('token'),
            'expires_in'   => static::propInt('过期时间（秒）'),
            'access_token' => static::propString('密钥'),
        ];
    }
}