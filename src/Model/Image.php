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
        'key', // 该字段在数据库中文件名和后缀以 . 分隔，但在接口的输入输出中，以 _ 分隔。因为请求的 url 不能图片后缀结尾
        'filename',
        'width',
        'height',
        'create_time',
        'item_type',
        'item_id',
        'user_id',
    ];

    public $allowOrderFields = [
        'create_time'
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
            ->order($this->getOrderFromRequest(['create_time' => 'DESC']))
            ->paginate();
    }
}
