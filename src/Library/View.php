<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Constant\OptionConstant;
use MDClub\Facade\Library\Option as OptionFacade;
use MDClub\Initializer\App;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;

/**
 * 模板渲染
 */
class View
{
    /**
     * @var PhpRenderer
     */
    protected $phpRenderer;

    /**
     * 默认主题
     *
     * @var string
     */
    protected $defaultTheme = 'default';

    public function __construct()
    {
        $this->phpRenderer = new PhpRenderer(__DIR__ . '/../../templates/');
    }

    /**
     * 返回渲染后的模板字符串
     *
     * @param  string $template
     * @param  array  $data
     * @return mixed
     */
    public function fetch(string $template, array $data = [])
    {
        $customTheme = OptionFacade::get(OptionConstant::THEME);

        $theme = is_file($this->phpRenderer->getTemplatePath() . $customTheme . $template)
            ? $customTheme
            : $this->defaultTheme;

        return $this->phpRenderer->fetch("/{$theme}{$template}", $data);
    }

    /**
     * 渲染模板
     *
     * @param  string $template
     * @param  array  $data
     * @return ResponseInterface
     */
    public function render(string $template, array $data = []): ResponseInterface
    {
        $output = $this->fetch($template, $data);

        /** @var $response ResponseInterface */
        $response = App::$container->get(ResponseInterface::class);
        $response = $response->withHeader('Content-Type', 'text/html; charset=utf-8');
        $response->getBody()->write($output);

        return $response;
    }
}
