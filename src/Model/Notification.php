<?php

declare(strict_types=1);

namespace MDClub\Model;

/**
 * 通知模型
 */
class Notification extends Abstracts
{
    public $table = 'notification';
    public $primaryKey = 'notification_id';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'notification_id',
        'receiver_id',
        'sender_id',
        'type',
        'article_id',
        'question_id',
        'answer_id',
        'comment_id',
        'reply_id',
        'content_deleted',
        'create_time',
        'read_time'
    ];

    public $allowFilterFields = [
        'type',
        'read', // read 为 boolean 值，需要另外写罗辑
    ];

    /**
     * @inheritDoc
     */
    protected function beforeInsert(array $data): array
    {
        return collect($data)->union([
            'sender_id'       => 0,
            'article_id'      => 0,
            'question_id'     => 0,
            'answer_id'       => 0,
            'comment_id'      => 0,
            'reply_id'        => 0,
            'content_deleted' => '',
        ])->all();
    }

    /**
     * @inheritDoc
     */
    public function getWhereFromRequest(array $defaultFilter = []): array
    {
        $where = parent::getWhereFromRequest($defaultFilter);

        if (isset($where['read'])) {
            $where['read'] === 'true'
                ? $where['read_time[>]'] = 0
                : $where['read_time'] = 0;

            unset($where['read']);
        }

        return $where;
    }
}
