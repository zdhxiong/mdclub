<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ContainerAbstracts;

/**
 * 请求相关的方法
 */
class Request extends ContainerAbstracts
{
    /**
     * 获取 REQUEST_TIME 时间戳
     *
     * @return int
     */
    public function time(): int
    {
        return (int) $this->request->getServerParam('REQUEST_TIME');
    }

    /**
     * 判断当前请求是否支持 webp 图片格式
     *
     * @return bool
     */
    public function isSupportWebp(): bool
    {
        return strpos($this->request->getServerParam('HTTP_ACCEPT'), 'image/webp') > -1;
    }

    /**
     * 把 query 参数中用 , 分隔的参数转换为数组
     *
     * @param  string     $param   query参数名
     * @param  int        $count   数组中最大条目数
     * @return array|null          若存在参数，但值为空，则返回空数组；若不存在参数，则返回 null
     */
    public function getQueryParamToArray(string $param, int $count = 100): ?array
    {
        $value = $this->request->getQueryParam($param);

        if ($value === null) {
            return null;
        }

        return collect(explode(',', $value))->slice(0, $count)->filter()->unique()->all();
    }

    /**
     * 把 body 中用 , 分隔的参数转换为数组
     *
     * @param  string     $param
     * @param  int        $count
     * @return array|null        若存在参数，但值为空，则返回空数组；若不存在参数，则返回 null
     */
    public function getParsedBodyParamToArray(string $param, int $count = 100): ?array
    {
        $value = $this->request->getParsedBodyParam($param);

        if ($value === null) {
            return null;
        }

        return collect(explode(',', $value))->slice(0, $count)->filter()->unique()->all();
    }
}
