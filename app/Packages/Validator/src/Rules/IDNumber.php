<?php

declare(strict_types=1);

namespace Package\Validator\Rules;

/**
 * 身份证号码校验
 *
 * Class IDNumber
 *
 * @package Package\Validator
 */
class IDNumber
{
    /**
     * 权重因子
     *
     * @var array
     */
    protected static $weights = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1];

    /**
     * 获取身份证校验码
     *
     * @param string $IDNumber
     *
     * @return string
     */
    public static function getCheckCode(string $IDNumber): ?string
    {
        if (strlen($IDNumber) < 17) {
            return null;
        }

        $checkSum = 0;
        for ($i = 0; $i < 17; $i++) {
            $checkSum += $IDNumber[$i] * static::$weights[$i];
        }

        $result = (12 - $checkSum % 11) % 11;
        return $result < 10 ? (string)$result : 'X';
    }

    /**
     * 校验身份证号码
     *
     * @param string $IDNumber
     *
     * @return bool
     */
    public static function validate(string $IDNumber): bool
    {
        if (!preg_match("/^[1-9][0-9]{16}[0-9X]$/", $IDNumber)) {
            return false;
        }

        return $IDNumber[17] === static::getCheckCode($IDNumber);
    }

    /**
     * 生成身份证号码
     *
     * @param int $maxAge
     * @param int $minAge
     *
     * @return string
     */
    public static function generate(int $minAge = 20, int $maxAge = 55): string
    {
        // 随机在x-y岁之间生成年月日
        $now           = now();
        $minTimestamp  = $now->copy()->subYears($maxAge)->startOfYear()->getTimestamp();
        $maxTimestamp  = $now->subYears($minAge)->endOfYear()->getTimestamp();
        $randTimestamp = mt_rand($minTimestamp, $maxTimestamp);

        $IDCardNumber = implode([
            sprintf('%02d', mt_rand(11, 65)),
            sprintf('%02d', mt_rand(1, 20)),
            sprintf('%02d', mt_rand(1, 99)),
            $now->setTimestamp($randTimestamp)->format('Ymd'),
            sprintf('%03d', mt_rand(1, 999)),
        ]);

        return $IDCardNumber . static::getCheckCode($IDCardNumber);

    }
}
