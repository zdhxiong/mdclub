<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use MDClub\Facade\Service\QuestionService;
use Psr\Http\Message\ResponseInterface;

/**
 * 问答页面
 */
class Question
{
    /**
     * 问答列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/question/index.php');
    }

    /**
     * 问答详情页
     *
     * @param int $questionId
     *
     * @return ResponseInterface
     */
    public function info(int $questionId): ResponseInterface
    {
        return View::render(
            '/question/info.php',
            [
                'question' => QuestionService::getOrFail($questionId),
            ]
        );
    }
}
