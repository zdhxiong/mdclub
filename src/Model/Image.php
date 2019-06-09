<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * 图片模型
 */
class Image extends ModelAbstracts
{
    public $table = 'image';
    public $primaryKey = 'hash';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'hash',
        'filename',
        'width',
        'height',
        'create_time',
        'item_type',
        'item_id',
        'user_id',
    ];

    protected function beforeInsert(array $data): array
    {
        return collect($data)->union([
            'item_type' => null,
            'item_id'   => 0,
        ])->all();
    }
}
