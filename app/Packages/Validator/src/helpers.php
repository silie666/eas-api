<?php

if (!function_exists('validate_single')) {

    /**
     * Validate single data by special rule.
     *
     * @param             $value
     * @param string      $rule
     * @param string|null $message
     * @param string|null $attribute
     *
     * @return string|null
     */
    function validate_single($value, string $rule, ?string $message = null, ?string $attribute = null): ?string
    {
        $messages = is_null($message) ? [] : ['data.' . $rule => $message];

        if (is_null($attribute)) {
            $attribute = $value;
        }

        $validator = \Validator::make(
            ['data' => $value],
            ['data' => $rule],
            $messages,
            ['data' => $attribute]
        );
        return $validator->errors()->first() ?: null;
    }
}

if (!function_exists('validate_array')) {

    /**
     * Validate array data by special rule.
     *
     * @param array       $values
     * @param string      $rule
     * @param string|null $message
     * @param string|null $attribute
     *
     * @return string|null
     */
    function validate_array(array $values, string $rule, ?string $message = null, ?string $attribute = null): ?string
    {
        $messages = is_null($message) ? [] : ['data.*.' . $rule => $message];

        $attributes = [];
        foreach ($values as $key => $value) {
            $attributes['data.' . $key] = is_null($attribute) ? $value : $attribute;
        }

        $validator = \Validator::make(
            ['data' => $values],
            ['data.*' => $rule],
            $messages,
            $attributes
        );

        return $validator->errors()->first() ?: null;
    }
}
