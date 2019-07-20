<?php

declare(strict_types=1);

namespace MDClub\Controller;

use MDClub\Helper\Request;
use MDClub\Middleware\NeedManager;
use Psr\Http\Message\ResponseInterface;

/**
 * 问答
 */
class Question extends Abstracts
{
    /**
     * 问答列表页
     *
     * @return ResponseInterface
     */
    public function pageIndex(): ResponseInterface
    {
        return $this->render('/question/index.php');
    }

    /**
     * 问答详情页
     *
     * @param  int               $question_id
     * @return ResponseInterface
     */
    public function pageInfo(int $question_id): ResponseInterface
    {
        return $this->render('/question/info.php');
    }
}
