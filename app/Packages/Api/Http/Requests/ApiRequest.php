<?php

namespace Package\Api\Http\Requests;

use Carbon\CarbonInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

abstract class ApiRequest extends FormRequest
{
    /**
     * 日期时间格式
     *
     * @var string
     */
    protected $dateTimeFormat = DATE_RFC3339;

    /**
     * 日期时间校验规则，用于判断字段是否日期时间
     *
     * @var string
     */
    protected $dateTimeRule = 'date_format:' . DATE_RFC3339;

    /**
     * 数值处理
     *
     * @var array
     */
    protected array $numberConverter = [];

    /**
     * 获取日期格式的输入
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return \Carbon\CarbonInterface|mixed
     */
    public function inputDateTime($key, $default = null)
    {
        return $this->convertToDateTime($this->input($key), $default);
    }

    /**
     * 某日期的 00:00:00
     *
     * @param string $key
     * @param null   $default
     *
     * @return \Carbon\CarbonInterface|mixed
     */
    public function inputDateTimeOfStart($key, $default = null)
    {
        $dateTime = $this->inputDateTime($key, $default);
        if ($dateTime instanceof CarbonInterface) {
            return $dateTime->startOfDay();
        }

        return $dateTime;
    }

    /**
     * 某日期的 23:59:59
     *
     * @param string $key
     * @param null   $default
     *
     * @return \Carbon\CarbonInterface|mixed
     */
    public function inputDateTimeOfEnd($key, $default = null)
    {
        $dateTime = $this->inputDateTime($key, $default);
        if ($dateTime instanceof CarbonInterface) {
            return $dateTime->endOfDay();
        }

        return $dateTime;
    }

    /**
     * Get the validated data from the request.
     *
     * @param string|null       $key
     * @param string|array|null $default
     *
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $rules = $this->container->call([$this, 'rules']);
        // 转成N维数组并且判断是否是日期规则
        $validatedKeys = [];
        foreach ($rules as $ruleKey => $rule) {
            Arr::set($validatedKeys, $ruleKey, $this->hasDateTimeRule($rule));
        }

        $validatedArray = $this->validatedArray($validatedKeys, $this->all());

        $flattens = \Arr::dot($validatedArray);
        foreach ($flattens as $flattenKey => $flatten) {
            if (in_array($flatten, ['true', 'false'])) {
                $flattens[$flattenKey] = filter_var($flatten, FILTER_VALIDATE_BOOLEAN);
            }
            $replaced = \Str::of($flattenKey)->replaceMatches('/\d+/', '*')->toString();
            if (in_array($replaced, array_keys($this->numberConverter)) && is_numeric($flatten)) {
                $flattens[$flattenKey] = (int)bcmul($flatten, $this->numberConverter[$replaced]);
            }
        }
        $validatedArray = \Arr::undot($flattens);

        return data_get($validatedArray, $key, $default);
    }

    /**
     * 过滤未设置规则的字段
     *
     * @param array $arrayKeys
     * @param array $arrayData
     *
     * @return array|null
     */
    protected function validatedArray(array $arrayKeys, array $arrayData): ?array
    {
        if (!Arr::accessible($arrayKeys) || !Arr::accessible($arrayData)) {
            return null;
        }
        $validated = [];

        foreach ($arrayKeys as $key => $hasDateTimeRule) {

//            if (!isset($arrayData[$key])) {
//                continue;
//            }
//            if($key === 'to') dump($arrayData);

            if (is_array($hasDateTimeRule)) {
                $subArrayKeys = $hasDateTimeRule;
                $subArrayData = (array)Arr::get($arrayData, $key, []);

                reset($subArrayKeys);
                if (key($subArrayKeys) === '*') {
                    // 如果子集第一个key是*，则进行foreach遍历子集过滤
                    $subValidated    = [];
                    $subSubArrayKeys = $subArrayKeys['*'];
                    if (is_bool($subSubArrayKeys)) {
                        // 'xxx.*' => 'string'
                        $subValidated = $subArrayData;
                    } else {
                        // 'xxx.*.yyy' => 'string'
                        foreach ($subArrayData as $subSubArrayData) {
                            $subValidated[] = $this->validatedArray($subSubArrayKeys, $subSubArrayData);
                        }
                    }
                    $validated[$key] = $subValidated;
                } else {
                    // 否则遍历后赋值
                    $validated[$key] = $this->validatedArray($subArrayKeys, $subArrayData);
                }
            } elseif (is_bool($hasDateTimeRule)) {

                if (array_key_exists($key, $arrayData)) {
                    // 直接赋值，并且转换为日期格式
                    $value           = $arrayData[$key] ?? null;
                    $validated[$key] = $hasDateTimeRule && $value ? $this->convertToDateTime($value) : $value;
                }
            }
        }

        return $validated;
    }

    /**
     * 检查是否为日期字段
     *
     * @param string|array $rules
     *
     * @return bool
     */
    protected function hasDateTimeRule($rules): bool
    {
        $dateTimeRule = $this->dateTimeRule;
        foreach ((array)$rules as $rule) {
            if (is_string($rule) && mb_strpos($rule, $dateTimeRule) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 将值转换为日期
     *
     * @param mixed $value
     * @param mixed $default
     *
     * @return \Carbon\CarbonInterface|mixed
     */
    protected function convertToDateTime($value, $default = null)
    {
        if (is_null($value)) {
            return $default;
        }

        try {
            // 替换日期字段内容为日期对象
            return Date::createFromFormat($this->dateTimeFormat, $value);
        } catch (\Exception $e) {
        }

        return $default;
    }
}
