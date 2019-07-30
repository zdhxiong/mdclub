<?php

declare(strict_types=1);

namespace MDClub\Service\Answer;

use MDClub\Exception\ValidationException;
use MDClub\Helper\Html;
use MDClub\Helper\Markdown;
use MDClub\Helper\Validator;
use MDClub\Service\Abstracts as ServiceAbstract;
use Psr\Container\ContainerInterface;

/**
 * 回答抽象类
 */
abstract class Abstracts extends ServiceAbstract
{
    /**
     * @var \MDClub\Model\Answer
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->answerModel;
    }

    /**
     * 发表和更新回答前对参数进行验证，并返回处理后的内容
     *
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @return array
     */
    protected function handleContent(string $contentMarkdown, string $contentRendered): array
    {
        $contentMarkdown = Html::removeXss($contentMarkdown);
        $contentRendered = Html::removeXss($contentRendered);

        // content_markdown 和 content_rendered 至少需传入一个；都传入时，以 content_markdown 为准
        if (!$contentMarkdown && !$contentRendered) {
            $error = '正文不能为空';
        } elseif (!$contentMarkdown) {
            $contentMarkdown = Html::toMarkdown($contentRendered);
        } else {
            $contentRendered = Markdown::toHtml($contentMarkdown);
        }

        // 验证正文长度
        $isTooLong = Validator::isMin(strip_tags($contentRendered), 100000);
        if (empty($error) && $isTooLong) {
            $error = '正文不能超过 100000 个字';
        }

        if (!empty($error)) {
            throw new ValidationException([
                'content_markdown' => $error,
                'content_rendered' => $error,
            ]);
        }

        return [
            'content_markdown' => $contentMarkdown,
            'content_rendered' => $contentRendered,
        ];
    }
}
