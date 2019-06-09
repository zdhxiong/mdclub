<?php

declare(strict_types=1);

namespace App\Helper;

use Zhuzhichao\IpLocationZh\Ip;

/**
 * IP 相关方法
 */
class IpHelper
{
    /**
     * 获取 IP 地址
     *
     * @return string
     */
    public static function getIp(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * 获取 IP 对应的地区
     *
     * @param  string $ip 若未传入 ip 参数，则自动获取当前IP
     * @return string
     */
    public static function getLocation($ip = null): string
    {
        if (!$ip) {
            $ip = self::getIp();
        }

        $arr = Ip::find($ip);

        if (is_array($arr)) {
            array_pop($arr);
            return trim(implode(' ', array_unique($arr)));
        }

        return '';
    }
}
