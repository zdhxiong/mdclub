<?php

declare(strict_types=1);

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use Tightenco\Collect\Support\Collection;

/**
 * 扩展 Collection
 */
class CollectionMacro
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        /**
         * 将集合渲染成 JSON 并输出
         */
        Collection::macro('render', function (Response $response): Response {
            /** @var Collection $this */
            $data = $this->all();
            $result = ['code' => 0];

            if (isset($data['data'], $data['pagination'])) {
                $result['data'] = $data['data'];
                $result['pagination'] = $data['pagination'];
            } else {
                $result['data'] = $data;
            }

            return $response->withJson($result);
        });

        return $next($request, $response);
    }
}
