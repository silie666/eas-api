<?php

namespace Package\Validator;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Str;

class ValidateRuleExtend
{
    /**
     * 整型范围限制
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function validateSilieInt($attribute, $value, $parameters, $validator)
    {
        // 判断是否整型
        if (!$validator->validateInteger($attribute, $value)) {
            return false;
        }

        $type     = $parameters[0] ?? null;
        $isSigned = ($parameters[1] ?? null) !== 'unsigned';
        return Rules\SilieInt::validate($value, $type, $isSigned);
    }

    /**
     * 验证手机号码规则
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     */
    public function validateSilieCellphoneNumber($attribute, $value, $parameters, $validator): bool
    {
        return preg_match('/^1[0-9]{10}$/', $value) === 1;
    }

    /**
     * 验证float（默认两位）
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     */
    public function validateFloat($attribute, $value, $parameters, $validator): bool
    {
        $num = $parameters[0] ?? 2;
        return preg_match("/^\d+(\.\d{1,$num})?$/", $value) === 1;
    }

    /**
     * 验证布尔值(只为了搜索用)
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     */
    public function validateSilieBool($attribute, $value, $parameters, $validator): bool
    {
        $acceptable = [true, false, 0, 1, '0', '1', 'true', 'false'];

        return in_array($value, $acceptable, true);
    }


    /**
     * 验证是否有效身份证
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     */
    public function validateSilieIdNumber($attribute, $value, $parameters, $validator): bool
    {
        if (!is_string($value) || strlen($value) !== 18) {
            return false;
        }

        return Rules\IDNumber::validate(strtoupper($value));
    }

    /**
     * 将扩展规则添加到validator中
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     */
    public static function addTo(Factory $factory)
    {
        $className = static::class;
        foreach (get_class_methods($className) as $method) {
            if (strpos($method, 'validate') === 0) {
                $validateName = Str::snake(substr($method, 8));
                $factory->extend($validateName, $className . '@' . $method,
                    trans('Package-validator::validation.' . $validateName));
            }
        }
    }

    /**
     * Require a certain number of parameters to be present.
     *
     * @param int    $count
     * @param array  $parameters
     * @param string $rule
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function requireParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new \InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }
}
