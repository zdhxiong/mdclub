<?php

declare(strict_types=1);

namespace MDClub\Traits;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 可获取单个项目或列表的对象（answer, article, comment, question, report, topic, user）
 *
 * @property-read \MDClub\Model\Abstracts $model
 * @property-read ServerRequestInterface  $request
 */
trait Getable
{
    /**
     * 判断指定对象是否存在
     *
     * @param  int|string  $id
     * @return bool
     */
    public function has($id): bool
    {
        return $this->model->has($id);
    }

    /**
     * 若对象不存在，则抛出异常
     *
     * @param  int|string  $id
     */
    public function hasOrFail($id): void
    {
        if (!$this->has($id)) {
            $this->throwNotFoundException();
        }
    }

    /**
     * 根据对象的ID数组判断这些对象是否存在
     *
     * @param  array $ids
     * @return array       键名为对象ID，键值为bool值
     */
    public function hasMultiple(array $ids): array
    {
        $primaryKey = $this->model->primaryKey;
        $result = [];

        if (!$ids = array_unique($ids)) {
            return $result;
        }

        $existIds = $this->model
            ->where($primaryKey, $ids)
            ->pluck($primaryKey);

        foreach ($ids as $id) {
            $result[$id] = in_array($id, $existIds, true);
        }

        return $result;
    }

    /**
     * 获取对象信息
     *
     * @param  int|string  $id
     * @return array|null
     */
    public function get($id): ?array
    {
        return $this->model->get($id);
    }

    /**
     * 获取对象信息，不存在则抛出异常
     *
     * @param  int|string  $id
     * @return array
     */
    public function getOrFail($id): array
    {
        $data = $this->get($id);

        if (!$data) {
            $this->throwNotFoundException();
        }

        return $data;
    }

    /**
     * 获取多个对象信息
     *
     * @param  array $ids
     * @return array
     */
    public function getMultiple(array $ids): array
    {
        if (!$ids) {
            return [];
        }

        return $this->model->select($ids);
    }

    /**
     * 抛出对象不存在的异常
     */
    protected function throwNotFoundException(): void
    {
        $table = $this->model->table;
        $exceptions = [
            'answer'   => ApiError::ANSWER_NOT_FOUND,
            'article'  => ApiError::ARTICLE_NOT_FOUND,
            'comment'  => ApiError::COMMENT_NOT_FOUND,
            'image'    => ApiError::IMAGE_NOT_FOUND,
            'question' => ApiError::QUESTION_NOT_FOUND,
            'report'   => ApiError::REPORT_NOT_FOUND,
            'topic'    => ApiError::TOPIC_NOT_FOUND,
            'user'     => ApiError::USER_NOT_FOUND,
        ];

        throw new ApiException($exceptions[$table]);
    }
}
