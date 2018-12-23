<?php

declare(strict_types=1);

namespace App\Handlers;

use Psr\Container\ContainerInterface;

/**
 * PhpRenderer 重写，使其具有主题功能
 *
 * Class PhpRenderer
 * @package App\Handlers
 */
class PhpRenderer extends \Slim\Views\PhpRenderer
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
     * PhpRenderer constructor.
     *
     * @param ContainerInterface $container
     * @param string $templatePath
     */
    public function __construct(ContainerInterface $container, string $templatePath)
    {
        parent::__construct($templatePath, []);

        /** @var \App\Service\OptionService $optionService */
        $optionService = $container->get(\App\Service\OptionService::class);
        $this->theme = $optionService->getAll()['theme'];
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
