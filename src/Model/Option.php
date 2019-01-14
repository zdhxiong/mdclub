<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * Class Option
 * @package App\Model
 */
class Option extends ModelAbstracts
{
    public $table = 'option';
    public $primaryKey = 'name';

    public $columns = [
        'name',
        'value',
    ];
}
