<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Option as OptionObserver;

/**
 * 配置模型
 */
class Option extends Abstracts
{
    public $table = 'option';
    public $primaryKey = 'name';
    protected $observe = OptionObserver::class;

    public $columns = [
        'name',
        'value',
    ];
}
