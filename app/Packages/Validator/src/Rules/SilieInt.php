<?php

declare(strict_types=1);

namespace Package\Validator\Rules;

class SilieInt
{
    /**
     * 整型规则
     *
     * @var array
     */
    protected static $intRules = [
        'tinyint'   => [
            'signed'   => [-128, 127],
            'unsigned' => [0, 255],
        ],
        'smallint'  => [
            'signed'   => [-32768, 32767],
            'unsigned' => [0, 65535],
        ],
        'mediumint' => [
            'signed'   => [-8388608, 8388607],
            'unsigned' => [0, 16777215],
        ],
        'int'       => [
            'signed'   => [-2147483648, 2147483647],
            'unsigned' => [0, 4294967295],
        ],
        'bigint'    => [
            // 可能会超过php int最大值，所以用字符串存储
            'signed'   => ['-9223372036854775808', '9223372036854775807'],
            'unsigned' => [0, '18446744073709551615'],
        ],
    ];

    /**
     * 判断是否存在某类型的整形
     *
     * @param string|null $type
     *
     * @return bool
     */
    public static function hasType(?string $type): bool
    {
        return array_key_exists($type . 'int', static::$intRules);
    }

    /**
     * 获取对应类型的范围
     *
     * @param string|null $type
     * @param bool        $isSigned
     *
     * @return array
     */
    public static function getTypeRange(?string $type, bool $isSigned): array
    {
        if (!static::hasType($type)) {
            throw new \InvalidArgumentException("类型 [$type] 不存在");
        }

        return static::$intRules[$type . 'int'][$isSigned ? 'signed' : 'unsigned'];
    }

    /**
     * 验证整形是否符合规则
     *
     * @param int         $number
     * @param string|null $type
     * @param bool        $isSigned
     *
     * @return bool
     */
    public static function validate(int $number, ?string $type, bool $isSigned): bool
    {
        [$min, $max] = static::getTypeRange($type, $isSigned);
        return $number >= $min && $number <= $max;
    }
}
