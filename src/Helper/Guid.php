<?php

declare(strict_types=1);

namespace MDClub\Helper;

/**
 * Guid
 */
class Guid
{
    /**
     * 生成 guid
     *
     * @return string
     */
    public static function generate(): string
    {
        if (function_exists('com_create_guid')) {
            $guid = trim(com_create_guid(), '{}');

            return strtolower(str_replace('-', '', $guid));
        }

        mt_srand((int)microtime()*10000);

        return md5(uniqid((string)mt_rand(), true));
    }
}
