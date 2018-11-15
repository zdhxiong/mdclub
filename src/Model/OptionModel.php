<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * Class OptionModel
 *
 * @package App\Model
 */
class OptionModel extends ModelAbstracts
{
    public $table = 'option';
    public $primaryKey = 'name';

    public $columns = [
        'name',
        'value',
    ];
}
