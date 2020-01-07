<?php

declare(strict_types=1);

namespace MDClub\Model;

/**
 * 配置模型
 */
class Option extends Abstracts
{
    public $table = 'option';
    public $primaryKey = 'name';

    public $columns = [
        'name',
        'value',
    ];
}
