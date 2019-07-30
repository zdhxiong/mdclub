<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Controller\Abstracts;
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
    public function index(): ResponseInterface
    {
        return $this->render('/question/index.php');
    }

    /**
     * 问答详情页
     *
     * @param  int               $question_id
     * @return ResponseInterface
     */
    public function info(int $question_id): ResponseInterface
    {
        return $this->render('/question/info.php');
    }
}
