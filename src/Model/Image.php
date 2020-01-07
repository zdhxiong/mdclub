<?php

declare(strict_types=1);

namespace MDClub\Model;

/**
 * 图片模型
 */
class Image extends Abstracts
{
    public $table = 'image';
    public $primaryKey = 'key';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'key',
        'filename',
        'width',
        'height',
        'create_time',
        'item_type',
        'item_id',
        'user_id',
    ];

    public $allowFilterFields = [
        'key',
        'item_type',
        'item_id',
        'user_id',
    ];

    /**
     * @inheritDoc
     */
    protected function beforeInsert(array $data): array
    {
        return collect($data)->union([
            'item_type' => null,
            'item_id'   => 0,
        ])->all();
    }

    /**
     * 根据 url 参数获取图片列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this
            ->where($this->getWhereFromRequest())
            ->order('create_time', 'DESC')
            ->paginate();
    }
}
