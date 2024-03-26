<?php

namespace Package\Api\Http;

use Illuminate\Http\JsonResponse;

class ApiResponse extends JsonResponse
{
    /**
     * 清空返回内容
     *
     * @return $this
     */
    public function emptyContent()
    {
        $this->content = null;
        return $this;
    }

}