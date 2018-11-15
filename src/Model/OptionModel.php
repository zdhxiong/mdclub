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
    protected $table = 'option';
    protected $primaryKey = 'name';

    public $columns = [
        'name',
        'value',
    ];
}
