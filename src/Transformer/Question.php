<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Model\QuestionModel;

/**
 * 提问转换器
 */
class Question extends Abstracts
{
    protected $table = 'question';
    protected $primaryKey = 'question_id';
    protected $availableIncludes = ['user', 'topics', 'is_following', 'voting'];
    protected $userExcept = ['delete_time'];

    /**
     * 格式化提问信息
     *
     * @param array $item
     * @return array
     */
    protected function format(array $item): array
    {
        if (isset($item['title'])) {
            $item['title'] = htmlentities($item['title']);
        }

        return $item;
    }

    /**
     * 获取 question 子资源
     *
     * @param  array $questionIds
     * @return array
     */
    public function getInRelationship(array $questionIds): array
    {
        if (!$questionIds) {
            return [];
        }

        $questions = QuestionModel::field(['question_id', 'title', 'create_time', 'update_time'])
            ->select($questionIds);

        return collect($questions)
            ->keyBy('question_id')
            ->map(function ($item) {
                $item['title'] = htmlentities($item['title']);

                return $item;
            })
            ->unionFill($questionIds)
            ->all();
    }
}
