<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * 可关注目标的接口
 *
 * Interface FollowableInterface
 * @package App\Interfaces
 */
interface FollowableInterface
{
    public function has(int $primaryKey): bool;

    public function addRelationship(array $items, array $relationship = []): array;
}
