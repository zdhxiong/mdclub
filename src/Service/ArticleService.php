<?php

declare(strict_types=1);

namespace App\Service;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Interfaces\FollowableInterface;

/**
 * 文章
 *
 * Class ArticleService
 * @package App\Service
 */
class ArticleService extends Service implements FollowableInterface
{
    /**
     * 判断指定文章是否存在
     *
     * @param  int  $articleId
     * @return bool
     */
    public function has(int $articleId): bool
    {
        return $this->articleModel->has($articleId);
    }

    /**
     * 获取文章信息
     *
     * @param  int   $articleId
     * @param  bool  $withRelationship
     * @return array
     */
    public function get(int $articleId, bool $withRelationship = false): array
    {
        $articleInfo = $this->articleModel->get($articleId);

        if (!$articleInfo) {
            throw new ApiException(ErrorConstant::ARTICLE_NOT_FOUND);
        }

        if ($withRelationship) {
            $articleInfo = $this->addRelationship($articleInfo);
        }

        return $articleInfo;
    }

    /**
     * @param array $primaryKeys
     * @param bool $withRelationship
     * @return array
     */
    public function getMultiple(array $primaryKeys, bool $withRelationship = false): array
    {
    }

    public function addRelationship(array $topics, array $relationship = []): array
    {
    }
}
