<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

use MDClub\Initializer\App;
use MDClub\Transformer\Abstracts as TransformerAbstracts;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;

/**
 * 转换器中间件抽象类
 */
abstract class Abstracts implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $transformer = App::$container->get($this->getTransformer());

        /** @var $instance TransformerAbstracts */
        $instance = new $transformer();

        $body = json_decode($response->getBody()->__toString(), true);
        $body['data'] = $instance->transform($body['data']);

        $json = (string) json_encode($body);
        $response = $response->withBody((new StreamFactory())->createStream($json));

        return $response;
    }

    /**
     * 获取转换器类名
     *
     * @return string
     */
    abstract protected function getTransformer(): string;
}
