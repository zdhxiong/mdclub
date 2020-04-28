<?php

declare(strict_types=1);

namespace MDClub\Service\Traits;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Request;
use MDClub\Initializer\App;
use MDClub\Model\Abstracts as ModelAbstracts;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

/**
 * 可获取对象。用于 question, answer, article, comment, topic, user, image, report
 */
trait Getable
{
    /**
     * @inheritDoc
     */
    abstract public function getModelInstance(): ModelAbstracts;

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        $model = $this->getModelInstance();

        return $model->has($id);
    }

    /**
     * @inheritDoc
     */
    public function hasOrFail($id): void
    {
        if (!$this->has($id)) {
            $this->throwNotFoundException();
        }
    }

    /**
     * @inheritDoc
     */
    public function hasMultiple(array $ids): array
    {
        $model = $this->getModelInstance();
        $primaryKey = $model->primaryKey;
        $result = [];

        if (!$ids = array_unique($ids)) {
            return $result;
        }

        $existIds = $model
            ->where($primaryKey, $ids)
            ->pluck($primaryKey);

        foreach ($ids as $id) {
            $result[$id] = in_array($id, $existIds, true);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function get($id): ?array
    {
        $model = $this->getModelInstance();

        return $model->get($id);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getMultiple(array $ids): array
    {
        if (!$ids) {
            return [];
        }

        $model = $this->getModelInstance();

        return $model->select($ids);
    }

    /**
     * @inheritDoc
     */
    public function getList(): array
    {
        $model = $this->getModelInstance();

        return $model->getList();
    }

    /**
     * 抛出对象不存在的异常
     */
    protected function throwNotFoundException(): void
    {
        $model = $this->getModelInstance();
        $table = $model->table;
        $exceptions = [
            'answer'   => ApiErrorConstant::ANSWER_NOT_FOUND,
            'article'  => ApiErrorConstant::ARTICLE_NOT_FOUND,
            'comment'  => ApiErrorConstant::COMMENT_NOT_FOUND,
            'image'    => ApiErrorConstant::COMMON_IMAGE_NOT_FOUND,
            'question' => ApiErrorConstant::QUESTION_NOT_FOUND,
            'report'   => ApiErrorConstant::REPORT_NOT_FOUND,
            'topic'    => ApiErrorConstant::TOPIC_NOT_FOUND,
            'user'     => ApiErrorConstant::USER_NOT_FOUND,
        ];

        if (Request::isJson()) {
            throw new ApiException($exceptions[$table]);
        } else {
            throw new HttpNotFoundException(App::$container->get(ServerRequestInterface::class));
        }
    }
}
