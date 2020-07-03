<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Model\AnswerModel;

/**
 * 回答转换器
 */
class Answer extends Abstracts
{
    protected $table = 'answer';
    protected $primaryKey = 'answer_id';
    protected $availableIncludes = ['user', 'question', 'voting'];
    protected $userExcept = ['delete_time'];

    /**
     * 获取 answer 子资源
     *
     * @param  array $answerIds
     * @return array
     */
    public function getInRelationship(array $answerIds): array
    {
        if (!$answerIds) {
            return [];
        }

        $answers = AnswerModel::field(['answer_id', 'question_id', 'content_rendered', 'create_time', 'update_time'])
            ->select($answerIds);

        return collect($answers)
            ->keyBy('answer_id')
            ->map(function ($item) {
                $item['content_summary'] = mb_substr(strip_tags($item['content_rendered']), 0, 80);
                unset($item['content_rendered']);

                return $item;
            })
            ->unionFill($answerIds)
            ->all();
    }
}
