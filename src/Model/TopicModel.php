<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class TopicModel
 *
 * @package App\Model
 */
class TopicModel extends Model
{
    protected $table = 'topic';
    protected $primaryKey = 'topic_id';
    protected $softDelete = true;

    protected $columns = [
        'topic_id',
        'name',
        'cover',
        'description',
        'article_count',
        'question_count',
        'follower_count',
        'delete_time',
    ];
}
