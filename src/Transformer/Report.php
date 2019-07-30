<?php

declare(strict_types=1);

namespace MDClub\Transformer;

/**
 * 举报转换器
 */
class Report extends Abstracts
{
    protected $availableIncludes = ['question', 'answer', 'article', 'comment', 'user'];

    /**
     * @inheritDoc
     */
    public function transform(array $items, array $knownRelationship = []): array
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
            $primaryKey = $map[$item['reportable_type']];
            $item[$primaryKey] = $item['reportable_id'];
        }

        $items = parent::transform($items, $knownRelationship);

        foreach ($items as &$item) {
            $primaryKey = $map[$item['reportable_type']];
            unset($item[$primaryKey]);
        }

        return $isSingleItem ? $items[0] : $items;
    }
}
