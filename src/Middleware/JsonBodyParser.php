<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 将请求 body 中的 json 解析成数组
 */
class JsonBodyParser implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (
            strstr($contentType, 'application/json') &&
            $input = file_get_contents('php://input')
        ) {
            $contents = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request = $request->withParsedBody($contents);
            } else {
                throw new ApiException(ApiErrorConstant::SYSTEM_REQUEST_JSON_INVALID);
            }
        }

        return $handler->handle($request);
    }
}
