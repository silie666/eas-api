<?php

namespace Package\Api\Http\Resources;

use DateTimeInterface;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = null;

    /**
     * Response default headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Options for encoding data to JSON.
     *
     * @var int
     */
    protected $encodingOptions = 0;

    /**
     * 通用日期格式
     *
     * @var string
     */
    protected static $dateTimeFormat = DATE_RFC3339;

    /**
     * Create new anonymous resource collection.
     *
     * @param mixed $resource
     *
     * @return static[]
     */
    public static function collection($resource)
    {
        return tap(new AnonymousResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return (new ResourceResponse($this))->toResponse($request, $this->headers, $this->encodingOptions);
    }

    /**
     * 日期格式化
     *
     * @param \DateTimeInterface|null $datetime
     * @param mixed|null              $default
     *
     * @return string|mixed
     */
    protected static function formatDateTime(?DateTimeInterface $datetime, mixed $default = null)
    {
        return $datetime ? $datetime->format(static::$dateTimeFormat) : $default;
    }
}
