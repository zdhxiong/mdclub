<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class ImageModel
 *
 * @package App\Model
 */
class ImageModel extends Model
{
    protected $table = 'image';
    protected $primaryKey = 'hash';
    protected $timestamps = true;

    const UPDATE_TIME = false;

    protected $columns = [
        'hash',
        'filename',
        'width',
        'height',
        'create_time',
        'item_type',
        'item_id',
    ];
}
