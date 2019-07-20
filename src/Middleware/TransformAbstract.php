<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;

/**
 * 转换器抽象类
 */
abstract class TransformAbstract extends Abstracts implements MiddlewareInterface
{
    /**
     * @var \MDClub\Transformer\Abstracts
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $body = json_decode($response->getBody()->__toString(), true);
        $body['data'] = $this->transformer->transform($body['data']);

        $json = (string) json_encode($body);

        $response = $response->withBody((new StreamFactory())->createStream($json));

        return $response;
    }
}
