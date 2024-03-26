<?php

declare(strict_types=1);

namespace Package\Api\Http\Resources;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class ApiDeclarativeResource extends ApiResource
{
    /**
     * 可转换的格式
     */
    const CAST_TYPE_BOOL   = 'bool';
    const CAST_TYPE_INT    = 'int';
    const CAST_TYPE_FLOAT  = 'float';
    const CAST_TYPE_DOUBLE = 'double';
    const CAST_TYPE_STRING = 'string';
    const CAST_TYPE_ARRAY  = 'array';
    const CAST_TYPE_OBJECT = 'object';
    const CAST_TYPE_NULL   = 'null';

    /**
     * @var array
     */
    protected $cachedAttributes = [];

    /**
     * 资源描述
     *
     * @return array
     */
    public static function schema(): array
    {
        return static::createSchema();
    }

    /**
     * 资源属性
     *
     * @return array
     */
    abstract public static function properties(): array;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        // 为空直接返回空对象
        if (empty($this->resource)) {
            return [];
        }

        $properties = static::properties();

        $result = [];
        foreach ($properties as $name => $property) {
            $result[$name] = $this->getAttribute($name, $property);
        }

        return $result;
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     * @param array  $property
     *
     * @return mixed
     */
    protected function getAttribute(string $key, array $property = [])
    {
        // 已有缓存数据，直接返回
        if (array_key_exists($key, $this->cachedAttributes)) {
            return $this->cachedAttributes[$key];
        }

        $valueKey = Arr::get($property, 'key', $key);
        $value    = null;
        if (is_array($this->resource)) {
            $value = Arr::get($this->resource, $valueKey);
        } elseif ($this->resource instanceof Model) {
            $value = $this->safeGetAttributeValue($this->resource, $valueKey);
        } elseif (is_object($this->resource) && isset($this->resource->{$valueKey})) {
            $value = $this->resource->{$valueKey};
        } elseif ($this->resource instanceof Collection) {
            $value = $this->resource->get($valueKey);
        }

        $type      = Arr::get($property, 'format');
        $divisor   = Arr::get($property, 'divisor');
        $precision = Arr::get($property, 'precision');
        if ($type === self::CAST_TYPE_FLOAT && $divisor && !is_null($value)) {
            $value = bcdiv((string)$value, (string)$divisor, $precision ?: strlen((string)$divisor) - 1);
        }

        $mutator = Arr::get($property, 'mutator');
        return $this->cachedAttributes[$key] = $mutator ? $mutator->call($this, $value, $key) : $value;
    }

    /**
     * 安全地获取跨级数据
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     * @param mixed                               $default
     *
     * @return mixed
     */
    private function safeGetAttributeValue(Model $model, string $key, $default = null)
    {
        $value = $model;
        foreach (explode('.', $key) as $segment) {
            if (!$value instanceof Model) {
                return value($default);
            }

            if ($value->relationLoaded($segment)) {
                $value = $value->getRelation($segment);
            } else {
                $value = $value->getAttributeValue($segment);
            }

        }

        return $value;
    }

    /**
     * 创建资源描述
     *
     * @param string $description
     * @param int    $statusCode
     *
     * @return array
     */
    protected static function createSchema(string $description = '操作成功', int $statusCode = 200): array
    {
        return ['description' => $description, 'statusCode' => $statusCode];
    }

    /**
     * 整型(int32)
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propInt(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        bool $nullable = false
    ): array {
        $mutator = $mutator ?: static::createCastMutator(self::CAST_TYPE_INT, $nullable);
        return static::createProperty($key, $desc, 'integer', 'int32', null, $mutator, $nullable);
    }

    /**
     * 可为空整型(int32)
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     *
     * @return array
     */
    protected static function propNullableInt(string $desc, ?string $key = null, ?Closure $mutator = null): array
    {
        return static::propInt($desc, $key, $mutator, true);
    }

    /**
     * 整型(int64)
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propBigInt(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        bool $nullable = false
    ): array {
        $mutator = $mutator ?: static::createCastMutator(self::CAST_TYPE_INT, $nullable);
        return static::createProperty($key, $desc, 'integer', 'int64', null, $mutator, $nullable);
    }

    /**
     * 可为空整型(int64)
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     *
     * @return array
     */
    protected static function propNullableBigInt(string $desc, ?string $key = null, ?Closure $mutator = null): array
    {
        return static::propBigInt($desc, $key, $mutator, true);
    }

    /**
     * 单精度浮点
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propFloat(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        bool $nullable = false
    ): array {
        $mutator = $mutator ?: static::createCastMutator(self::CAST_TYPE_FLOAT, $nullable);
        return static::createProperty($key, $desc, 'number', 'float', null, $mutator, $nullable);
    }

    /**
     * 可为空单精度浮点
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     *
     * @return array
     */
    protected static function propNullableFloat(string $desc, ?string $key = null, ?Closure $mutator = null): array
    {
        return static::propFloat($desc, $key, $mutator, true);
    }


    /**
     * 格式化为单精度浮点
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propFormatFloat(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        bool $nullable = false,
        int $divisor = 100,
        int $precision = null
    ): array {
        $mutator = $mutator ?: static::createCastMutator(self::CAST_TYPE_FLOAT, $nullable);
        return static::createProperty($key, $desc, 'number', 'float', null, $mutator, $nullable, $divisor, $precision);
    }

    /**
     * 可为空格式化单精度浮点
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     *
     * @return array
     */
    protected static function propNullableFormatFloat(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        int $divisor = 100,
        int $precision = null
    ): array {
        return static::propFormatFloat($desc, $key, $mutator, true, $divisor, $precision);
    }

    /**
     * 双精度浮点
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propDouble(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        bool $nullable = false
    ): array {
        $mutator = $mutator ?: static::createCastMutator(self::CAST_TYPE_DOUBLE, $nullable);
        return static::createProperty($key, $desc, 'number', 'double', null, $mutator, $nullable);
    }

    /**
     * 可为空单精度浮点
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     *
     * @return array
     */
    protected static function propNullableDouble(string $desc, ?string $key = null, ?Closure $mutator = null): array
    {
        return static::propDouble($desc, $key, $mutator, true);
    }

    /**
     * 字符串
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propString(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        bool $nullable = false
    ): array {
        $mutator = $mutator ?: static::createCastMutator(self::CAST_TYPE_STRING, $nullable);
        return static::createProperty($key, $desc, 'string', null, null, $mutator, $nullable);
    }

    /**
     * 可为空字符串
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     *
     * @return array
     */
    protected static function propNullableString(string $desc, ?string $key = null, ?Closure $mutator = null): array
    {
        return static::propString($desc, $key, $mutator, true);
    }

    /**
     * 布尔值
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propBool(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        bool $nullable = false
    ): array {
        $mutator = $mutator ?: static::createCastMutator(self::CAST_TYPE_BOOL, $nullable);
        return static::createProperty($key, $desc, 'boolean', null, null, $mutator, $nullable);
    }

    /**
     * 可为空的布尔值
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     *
     * @return array
     */
    protected static function propNullableBool(string $desc, ?string $key = null, ?Closure $mutator = null): array
    {
        return static::propBool($desc, $key, $mutator, true);
    }

    /**
     * 日期
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propDatetime(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        bool $nullable = false,
        $format = 'date-time',
    ): array {
        $mutator = $mutator ?: function ($value) {
            return static::formatDateTime($value);
        };
        return static::createProperty($key, $desc, 'string', 'date-time', null, $mutator, $nullable);
    }

    /**
     * 可为空日期
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     *
     * @return array
     */
    protected static function propNullableDatetime(string $desc, ?string $key = null, ?Closure $mutator = null): array
    {
        return static::propDatetime($desc, $key, $mutator, true);
    }

    /**
     * 数组
     *
     * @param string        $desc
     * @param string|null   $key
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propArray(
        string $desc,
        ?string $key = null,
        ?Closure $mutator = null,
        bool $nullable = false,
        string $format = 'string'
    ): array {
        $mutator = $mutator ?: static::createCastMutator(self::CAST_TYPE_ARRAY, $nullable);
        return static::createProperty($key, $desc, 'array', $format, null, $mutator, $nullable);
    }

    /**
     * 对象
     *
     * @param string        $desc
     * @param string        $class
     * @param string|null   $valueKey
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propObject(
        string $desc,
        string $class,
        ?string $valueKey = null,
        ?Closure $mutator = null,
        bool $nullable = false
    ): array {
        if (is_null($mutator)) {
            $mutator = function ($value, $key) use ($class, $valueKey) {
                return new $class(data_get($this->resource, $valueKey ?: $key));
            };
        }

        return static::createProperty($valueKey, $desc, 'object', null, $class, $mutator, $nullable);
    }

    /**
     * 对象列表
     *
     * @param string        $desc
     * @param string        $class
     * @param string|null   $valueKey
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function propCollection(
        string $desc,
        string $class,
        ?string $valueKey = null,
        ?Closure $mutator = null,
        bool $nullable = false
    ): array {
        if (is_null($mutator)) {
            $mutator = function ($value, $key) use ($class, $valueKey) {
                return $class::collection(collect(data_get($this->resource, $valueKey ?: $key)));
            };
        }

        return static::createProperty($valueKey, $desc, 'collection', null, $class, $mutator, $nullable);
    }

    /**
     * 加载的单个关联
     *
     * @param string      $desc
     * @param string      $class
     * @param string|null $valueKey
     *
     * @return array
     */
    protected static function propWhenLoadedModel(string $desc, string $class, ?string $valueKey = null): array
    {
        $mutator = function ($value, $key) use ($class, $valueKey) {
            $relation = $valueKey ?: lcfirst(Str::studly($key));
            return new $class($this->whenLoaded($relation));
        };
        return static::propObject($desc, $class, $valueKey, $mutator, true);
    }

    /**
     * 加载的多个关联
     *
     * @param string      $desc
     * @param string      $class
     * @param string|null $valueKey
     *
     * @return array
     */
    protected static function propWhenLoadedModels(string $desc, string $class, ?string $valueKey = null): array
    {
        $mutator = function ($value, $key) use ($class, $valueKey) {
            $relation = $valueKey ?: lcfirst(Str::studly($key));
            return $class::collection($this->whenLoaded($relation));
        };
        return static::propCollection($desc, $class, $valueKey, $mutator, true);
    }

    /**
     * 创建资源属性对象
     *
     * @param string|null   $key
     * @param string        $desc
     * @param string        $type
     * @param string|null   $format
     * @param string|null   $ref
     * @param \Closure|null $mutator
     * @param bool          $nullable
     *
     * @return array
     */
    protected static function createProperty(
        ?string $key,
        string $desc,
        string $type,
        ?string $format = null,
        ?string $ref = null,
        ?Closure $mutator = null,
        bool $nullable = false,
        ?int $divisor = null
    ) {
        $keys     = ['key', 'description', 'type', 'format', 'ref', 'mutator', 'nullable', 'divisor', 'precision'];
        $values   = func_get_args();
        $property = [];

        for ($i = 0; $i < func_num_args(); $i++) {
            $value = $values[$i];
            if (!is_null($value)) {
                $property[$keys[$i]] = $value;
            }
        }
        return $property;
    }

    /**
     * 创建类型转换匿名函数
     *
     * @param string $type
     * @param bool   $nullable
     *
     * @return \Closure
     */
    private static function createCastMutator(string $type, bool $nullable): Closure
    {
        return function ($value) use ($type, $nullable) {
            if ($nullable && is_null($value)) {
                return null;
            }

            settype($value, $type);
            return $value;
        };
    }
}
