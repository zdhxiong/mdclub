<?php

declare(strict_types=1);

namespace App\Helper;

use Slim\Http\Request;

/**
 * 请求相关方法
 *
 * Class RequestHelper
 * @package App\Helper
 */
class RequestHelper
{
    /**
     * 判断当前请求是否支持 webp 图片格式
     *
     * @param  Request $request
     * @return bool
     */
    public static function isSupportWebp(Request $request): bool
    {
        return strpos($request->getServerParam('HTTP_ACCEPT'), 'image/webp') > -1;
    }
}
