<?php

if (!function_exists('cons')) {

    /**
     * Get constant.
     *
     * @param string|null $key
     *
     * @return \Package\Constant\Constant|mixed|int
     */
    function cons(string $key = null)
    {
        $constant = app('constant');
        if (is_null($key)) {
            return $constant;
        }

        return $constant->get($key);
    }
}

if (!function_exists('single_validation_exception')) {
    /**
     * 抛出验证错误异常
     *
     * @param string $key
     * @param string $message
     *
     * @return \Package\Exceptions\Client\ValidationException
     */
    function single_validation_exception(
        string $key,
        string $message
    ): \Package\Exceptions\Client\ValidationException {
        return \Package\Exceptions\Client\ValidationException::withMessages([$key => $message]);
    }
}
