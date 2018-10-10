<?php

declare(strict_types=1);

namespace App\Helper;

/**
 * IP 相关方法
 *
 * Class IpHelper
 * @package App\Helper
 */
class IpHelper
{
    /**
     * 获取 IP 地址
     *
     * @return string
     */
    public static function get(): string
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return '0.0.0.0';
        }
    }
}
