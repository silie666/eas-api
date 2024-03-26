<?php
declare(strict_types=1);

namespace App\Exceptions;

use Throwable;
use Package\Api\Exceptions\ApiHandler as ParentApiHandler;

class ApiHandler extends ParentApiHandler
{
    /**
     * 异常处理
     *
     * @param \Throwable $e
     *
     * @return \Throwable
     */
    protected function prepareException(Throwable $e): Throwable
    {
        return parent::prepareException($e);
    }
}
