<?php

declare(strict_types=1);

namespace MDClub\Service\Answer;

use MDClub\Helper\Request;

/**
 * 发表回答
 */
class Create extends Abstracts
{
    /**
     * 发表回答
     *
     * @param  int    $questionId       提问ID
     * @param  string $contentMarkdown  Markdown格式正文
     * @param  string $contentRendered  HTML格式正文
     * @return int                      回答ID
     */
    public function create(int $questionId, string $contentMarkdown, string $contentRendered): int
    {
        $userId = $this->auth->userId();
        $this->questionGetService->hasOrFail($questionId);

        $data = $this->handleContent($contentMarkdown, $contentRendered);
        $data['question_id'] = $questionId;
        $data['user_id'] = $userId;

        // 添加回答
        $answerId = (int) $this->model->insert($data);

        // 作者的 answer_count + 1
        $this->userModel
            ->where('user_id', $userId)
            ->inc('answer_count')
            ->update();

        // 更新提问的 answer_count 和 last_answer_time 字段
        $this->questionModel
            ->where('question_id', $questionId)
            ->inc('answer_count')
            ->set('last_answer_time', Request::time($this->request))
            ->update();

        return $answerId;
    }
}
