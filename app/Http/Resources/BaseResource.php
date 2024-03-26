<?php

namespace App\Http\Resources;

use Package\Api\Http\Resources\ApiDeclarativeResource;

abstract class BaseResource extends ApiDeclarativeResource
{
    /**
     * 处理文件URL
     *
     * @param string|null $code
     * @param bool        $isDownLoad
     *
     * @return string|null
     */
    protected function fileUrl(?string $code, bool $isDownLoad = false): ?string
    {
        if (!$code) {
            return null;
        }
        if ($isDownLoad) {
            return config('app.url') . '/file/' . $code . '?is_download=1';
        } else {
            return config('app.url') . '/file/' . $code;
        }
    }
}
