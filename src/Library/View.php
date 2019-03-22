<?php

declare(strict_types=1);

namespace App\Library;

use App\Interfaces\ContainerInterface;
use Slim\Views\PhpRenderer;

/**
 * PhpRenderer 重写，使其具有主题功能
 *
 * Class View
 * @package App\Library
 */
class View extends PhpRenderer
{
    /**
     * 当前使用的主题
     *
     * @var string
     */
    protected $theme;

    /**
     * 默认主题
     *
     * @var string
     */
    protected $defaultTheme = 'default';

    /**
     * View constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        parent::__construct(__DIR__ . '/../../templates/', []);

        $this->theme = $container->optionService->theme;
    }

    /**
     * 返回渲染后的模板字符串
     *
     * @param  string $template
     * @param  array  $data
     * @return mixed
     */
    public function fetch($template, array $data = [])
    {
        $theme = is_file($this->templatePath . $this->theme. $template)
            ? $this->theme
            : $this->defaultTheme;

        return parent::fetch('/' . $theme . $template, $data);
    }
}
