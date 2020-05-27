<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Transformer\UserTransformer;

/**
 * 举报详情转换器
 */
class ReportReason extends Abstracts
{
    protected $availableIncludes = ['reporter', 'question', 'answer', 'article', 'comment', 'user'];

    /**
     * 格式化举报详情
     *
     * @param array $item
     * @return array
     */
    protected function format(array $item): array
    {
        if (isset($item['reason'])) {
            $item['reason'] = htmlentities($item['reason']);
        }

        return $item;
    }

    /**
     * 添加 reporter 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function reporter(array $items): array
    {
        $userIds = collect($items)->pluck('reporter_id')->unique()->filter()->all();
        $users = UserTransformer::getInRelationship($userIds);

        foreach ($items as &$item) {
            $item['relationships']['reporter'] = $users[$item['reporter_id']];
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function transform(array $items, array $knownRelationship = [], bool $canWithRelationships = true): array
    {
        if (!$items) {
            return $items;
        }

        $isSingleItem = !isset($items[0]);

        if ($isSingleItem) {
            $items = [$items];
        }

        // 举报目标类型 => 举报目标主键名
        $map = [
            'question' => 'question_id',
            'answer' => 'answer_id',
            'article' => 'article_id',
            'comment' => 'comment_id',
            'user' => 'user_id',
        ];

        foreach ($items as &$item) {
            $item['reporter_id'] = $item['user_id'];
            unset($item['user_id']);

            $primaryKey = $map[$item['reportable_type']];
            $item[$primaryKey] = $item['reportable_id'];
        }

        $items =  parent::transform($items, $knownRelationship);

        foreach ($items as &$item) {
            $primaryKey = $map[$item['reportable_type']];
            unset($item[$primaryKey]);

            $item['user_id'] = $item['reporter_id'];
            unset($item['reporter_id']);
        }

        return $isSingleItem ? $items[0] : $items;
    }
}
