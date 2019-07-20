<?php

declare(strict_types=1);

namespace MDClub\Helper;

/**
 * 随机字符生成
 */
class Random
{
    /**
     * 获取随机字符串
     *
     * @param  int     $length  字符串长度
     * @param  string  $chars   字符的集合
     * @return string
     */
    public static function generate(int $length, string $chars = ''): string
    {
        if (!$chars) {
            $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        }

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $result;
    }

    /**
     * 获取随机数值字符串
     *
     * @param  int $length 字符串长度
     * @return string
     */
    public static function generateNumber(int $length): string
    {
        return self::generate($length, '0123456789');
    }
}
