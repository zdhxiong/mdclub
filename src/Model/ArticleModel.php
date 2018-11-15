<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;
use App\Helper\ArrayHelper;

/**
 * Class ArticleModel
 *
 * @package App\Model
 */
class ArticleModel extends ModelAbstracts
{
    public $table = 'article';
    public $primaryKey = 'article_id';
    protected $timestamps = true;
    protected $softDelete = true;

    public $columns = [
        'article_id',
        'user_id',
        'title',
        'content_markdown',
        'content_rendered',
        'comment_count',
        'view_count',
        'follower_count',
        'vote_count',
        'create_time',
        'update_time',
        'delete_time',
    ];

    protected function beforeInsert(array $data): array
    {
        return ArrayHelper::fill($data, [
            'comment_count'  => 0,
            'view_count'     => 0,
            'follower_count' => 0,
            'vote_count'     => 0,
        ]);
    }
}
