<?php

declare(strict_types=1);

namespace App\Helper;

/**
 * 验证相关方法
 */
class ValidatorHelper
{
    /**
     * 是否只含字母
     *
     * @param  string $value
     * @return bool
     */
    public static function isAlpha(string $value): bool
    {
        return preg_match('/^[A-Za-z]+$/', $value) === 1;
    }

    /**
     * 是否只包含字母和数字
     *
     * @param  string $value
     * @return bool
     */
    public static function isAlphaNum(string $value): bool
    {
        return preg_match('/^[A-Za-z0-9]+$/', $value) === 1;
    }

    /**
     * 是否只包含汉字、字母和数字
     *
     * @param  string $value
     * @return bool
     */
    public static function isChsAlphaNum(string $value): bool
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', $value) === 1;
    }

    /**
     * 是否只包含汉字
     *
     * @param  string $value
     * @return bool
     */
    public static function isChs(string $value): bool
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $value) === 1;
    }

    /**
     * 是否是 IP 地址
     *
     * @param  string $value
     * @return bool
     */
    public static function isIp(string $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
    }

    /**
     * 是否是 URL
     *
     * @param  string $value
     * @return bool
     */
    public static function isUrl(string $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_URL);
    }

    /**
     * 是否是邮箱
     *
     * @param  string $value
     * @return bool
     */
    public static function isEmail(string $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 是否是手机号
     *
     * @param  string $value
     * @return bool
     */
    public static function isMobile(string $value): bool
    {
        return preg_match('/^1\d{10}$/', $value) === 1;
    }

    /**
     * 字符串长度是否在指定范围内
     *
     * @param  string $value
     * @param  int    $min
     * @param  int    $max
     * @return bool
     */
    public static function isLength(string $value, int $min, int $max = 0): bool
    {
        $length = mb_strlen($value, 'utf-8');

        if (!$max) {
            // 指定长度
            return $length === $min;
        }

        // 指定长度区间
        return $length >= $min && $length <= $max;
    }

    /**
     * 字符串长度是否小于指定值
     *
     * @param  string $value
     * @param  int    $max
     * @return bool
     */
    public static function isMax(string $value, int $max): bool
    {
        $length = mb_strlen($value, 'utf-8');

        return $length <= $max;
    }

    /**
     * 字符串长度是否大于指定值
     *
     * @param  string $value
     * @param  int    $min
     * @return bool
     */
    public static function isMin(string $value, int $min): bool
    {
        $length = mb_strlen($value, 'utf-8');

        return $length >= $min;
    }
}
