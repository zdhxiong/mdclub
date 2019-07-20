<?php

declare(strict_types=1);

use MDClub\Initializer\Collection;

/**
 * 创建集合
 *
 * @param  mixed      $value
 * @return Collection
 */
function collect($value = null) {
    return new Collection($value);
}
