<?php

declare(strict_types=1);

namespace App\Helper;

/**
 * GUID
 */
class StringHelper
{
    /**
     * 生成唯一 ID
     *
     * @return string
     */
    public static function guid(): string
    {
        mt_srand((int)microtime()*10000);

        return md5(uniqid((string)mt_rand(), true));
    }

    /**
     * 获取随机字符串
     *
     * @param  int     $length  字符串长度
     * @param  string  $chars   字符的集合
     * @return string
     */
    public static function rand(int $length, string $chars = ''): string
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
    public static function randNumber(int $length): string
    {
        return self::rand($length, '0123456789');
    }
}
