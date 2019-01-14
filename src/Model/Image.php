<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;
use App\Helper\ArrayHelper;

/**
 * Class Image
 * @package App\Model
 */
class Image extends ModelAbstracts
{
    public $table = 'image';
    public $primaryKey = 'hash';
    protected $timestamps = true;

    const UPDATE_TIME = false;

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
        return ArrayHelper::fill($data, [
            'item_type' => null,
            'item_id'   => 0,
        ]);
    }
}
