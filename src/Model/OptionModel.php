<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class OptionModel
 *
 * @package App\Model
 */
class OptionModel extends Model
{
    protected $table = 'option';
    protected $primaryKey = 'name';

    protected $columns = [
        'name',
        'value',
    ];
}
