<?php

declare(strict_types=1);

namespace App\Model;

use App\Helper\ArrayHelper;

/**
 * Class ArticleModel
 *
 * @package App\Model
 */
class ArticleModel extends Model
{
    protected $table = 'article';
    protected $primaryKey = 'article_id';
    protected $timestamps = true;
    protected $softDelete = true;

    protected $columns = [
        'article_id',
        'user_id',
        'title',
        'content_markdown',
        'content_rendered',
        'comment_count',
        'view_count',
        'follower_count',
        'create_time',
        'update_time',
        'delete_time',
    ];

    protected function beforeInsert(array $data): array
    {
        return ArrayHelper::fill($data, [
            'comment_count' => 0,
            'view_count' => 0,
            'follower_count' => 0,
        ]);
    }

    protected function afterSelect(array $data, bool $nest = true): array
    {
        if (!$nest) {
            $data = [$data];
        }

        return $data;
    }
}
