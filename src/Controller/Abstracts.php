<?php

declare(strict_types=1);

namespace MDClub\Controller;

use MDClub\Traits\ContainerProperty;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 控制器抽象类
 */
abstract class Abstracts
{
    use ContainerProperty;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get(string $name)
    {
        return $this->container->get($name);
    }

    /**
     * 渲染 HTML 模板
     *
     * @param  string             $template
     * @param  array              $data
     * @return ResponseInterface
     */
    public function render(string $template, array $data = []): ResponseInterface
    {
        return $this->view->render($this->response, $template, $data);
    }
}
