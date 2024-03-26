<?php

namespace Package\Exceptions\Client;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Class ValidationException
 * 提交的参数异常，客户端提交的参数不符合要求
 *
 * @package Package\Exceptions\Client
 */
class ValidationException extends BaseException
{
    /**
     * The validator instance.
     *
     * @var \Illuminate\Contracts\Validation\Validator
     */
    public $validator;

    /**
     * 返回的状态码
     *
     * @var int
     */
    public $statusCode = 422;

    /**
     * Create a new exception instance.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @param \Exception|null                            $previous
     * @param int                                        $code
     *
     * @return void
     */
    public function __construct($validator, \Throwable $previous = null, $code = 0)
    {
        parent::__construct($this->statusCode, static::summarize($validator), $previous, [], $code);

        $this->validator = $validator;
    }

    /**
     * Create a new validation exception from a plain array of messages.
     *
     * @param array $messages
     *
     * @return static
     */
    public static function withMessages(array $messages)
    {
        return new static(tap(ValidatorFacade::make([], []), function ($validator) use ($messages) {
            foreach ($messages as $key => $value) {
                foreach (Arr::wrap($value) as $message) {
                    $validator->errors()->add($key, $message);
                }
            }
        }));
    }

    /**
     * Create a error message summary from the validation errors.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return string
     */
    protected static function summarize($validator)
    {
        $messages = $validator->errors()->all();

        if (!count($messages)) {
            return '参数验证失败';
        }

        $message = array_shift($messages);

        if ($additional = count($messages)) {
            $message .= " (还有 {$additional} 个错误)";
        }

        return $message;
    }

    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function errors()
    {
        return $this->transformErrors($this->validator->errors()->messages());
    }

    private function transformErrors($errors)
    {
        $result = [];
        foreach ($errors as $key => $messages) {
            $parts   = explode('.', $key);
            $pointer = &$result;
            foreach ($parts as $part) {
                if (is_numeric($part)) {
                    $part = "num_$part";
                }
                if (!isset($pointer[$part])) {
                    $pointer[$part] = [];
                }
                $pointer = &$pointer[$part];
            }
            $pointer = $messages;
        }
        return $result;
    }

    /**
     * Set the HTTP status code to be used for the response.
     *
     * @param int $statusCode
     *
     * @return $this
     */
    public function statusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * 获取状态码
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
