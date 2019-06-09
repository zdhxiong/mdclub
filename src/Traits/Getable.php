<?php

declare(strict_types=1);

namespace App\Traits;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use Tightenco\Collect\Support\Collection;

/**
 * 可获取单个项目或列表的对象（answer, article, comment, question, report, topic, user）
 *
 * @property-read \App\Abstracts\ModelAbstracts $model
 * @property-read \Slim\Http\Request            $request
 */
trait Getable
{
    use fetchCollection;

    /**
     * 返回结果是否排除隐私字段
     *
     * @var bool
     */
    protected $excludePrivateFields = false;

    /**
     * 是否对数据库中取出的内容进行处理
     *
     * @var bool
     */
    protected $withFormatted = false;

    /**
     * 是否包含关联信息
     *
     * @var bool
     */
    protected $withRelationship = false;

    /**
     * 排除结果中的隐私字段
     *
     * @return Getable
     */
    public function excludePrivateFields(): self
    {
        $this->excludePrivateFields = true;

        return $this;
    }

    /**
     * 对结果中的数据进行处理
     *
     * @return Getable
     */
    public function withFormatted(): self
    {
        $this->withFormatted = true;

        return $this;
    }

    /**
     * 结果中添加关联信息
     *
     * @return Getable
     */
    public function withRelationship(): self
    {
        $this->withRelationship = true;

        return $this;
    }

    /**
     * 隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return [];
    }

    /**
     * 允许排序的字段
     *
     * @return array
     */
    public function getAllowOrderFields(): array
    {
        return [];
    }

    /**
     * 允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return [];
    }

    /**
     * 对结果中的数据进行处理
     *
     * @param  array $items 二维数组
     * @return array
     */
    public function addFormatted(array $items): array
    {
        return $items;
    }

    /**
     * 结果中添加关联信息
     *
     * @param  array $items 二维数组
     * @param  array $knownRelationship 已知的 relationship 参数。已经在这里指定的参数，不用再查询数据库
     * @return array
     */
    public function addRelationship(array $items, array $knownRelationship = []): array
    {
        return $items;
    }

    /**
     * 供返回 restful api 使用
     *
     * @return Getable
     */
    public function forApi(): self
    {
        return $this
            ->excludePrivateFields()
            ->withFormatted()
            ->withRelationship()
            ->fetchCollection();
    }

    /**
     * 获取查询列表时的排序
     *
     * order=field 表示 field ASC
     * order=-field 表示 field DESC
     *
     * @param  array $defaultOrder     默认排序；query 参数不存在时，该参数才生效
     * @param  array $allowOrderFields 允许排序的字段，为 null 时，通过 getAllowOrderFields 方法获取
     * @return array
     */
    public function getOrder(array $defaultOrder = [], array $allowOrderFields = null): array
    {
        $result = [];
        $order = $this->request->getQueryParam('order');

        if ($allowOrderFields === null) {
            $allowOrderFields = $this->getAllowOrderFields();
        }

        if ($order) {
            if (strpos($order, '-') === 0) {
                $result[substr($order, 1)] = 'DESC';
            } else {
                $result[$order] = 'ASC';
            }

            $result = collect($result)->only($allowOrderFields)->all();
        }

        if (!$result) {
            $result = $defaultOrder;
        }

        return $result;
    }

    /**
     * 查询列表时的条件
     *
     * @param  array $defaultFilter     默认条件。该条件将覆盖 query 中的同名参数
     * @param  array $allowFilterFields 允许作为条件的字段，为 null 时，通过 getAllowFilterFields 方法获取
     * @return array
     */
    public function getWhere(array $defaultFilter = [], array $allowFilterFields = null): array
    {
        if ($allowFilterFields === null) {
            $allowFilterFields = $this->getAllowFilterFields();
        }

        $result = $this->request->getQueryParams();
        $result = collect($result)->only($allowFilterFields)->merge($defaultFilter)->all();

        return $result;
    }

    /**
     * 判断指定对象是否存在
     *
     * @param  int  $id
     * @return bool
     */
    public function has(int $id): bool
    {
        return $this->model->has($id);
    }

    /**
     * 若对象不存在，则抛出异常
     *
     * @param  int  $id
     */
    public function hasOrFail(int $id): void
    {
        if (!$this->has($id)) {
            $this->throwNotFoundException();
        }
    }

    /**
     * 根据对象的ID数组判断这些对象是否存在
     *
     * @param  array            $ids
     * @return array|Collection      键名为对象ID，键值为bool值
     */
    public function hasMultiple(array $ids)
    {
        $result = [];

        if (!$ids = array_unique($ids)) {
            return $result;
        }

        $existIds = $this->model
            ->where($this->model->primaryKey, $ids)
            ->pluck($this->model->primaryKey);

        foreach ($ids as $id) {
            $result[$id] = in_array($id, $existIds, true);
        }

        return $this->returnArray($result);
    }

    /**
     * 获取数据的前置操作
     */
    public function beforeGet(): void
    {
        if ($this->excludePrivateFields) {
            $this->model->field($this->getPrivacyFields(), true);
        }

        $this->excludePrivateFields = false;
    }

    /**
     * 获取数据的后续操作
     *
     * @param  array $result
     * @param  array $knownRelationship 已知的 relationship 参数。已经在这里指定的参数，不用再查询数据库
     * @return array
     */
    public function afterGet(array $result, array $knownRelationship = []): array
    {
        $withPagination = isset($result['data'], $result['pagination']);
        $data = $withPagination ? $result['data'] : $result;

        if (!$data) {
            return $result;
        }

        if (!$isArray = is_array(current($data))) {
            $data = [$data];
        }

        if ($this->withFormatted) {
            $data = $this->addFormatted($data);
        }

        if ($this->withRelationship) {
            $data = $this->addRelationship($data, $knownRelationship);
        }

        $this->withFormatted = false;
        $this->withRelationship = false;

        if (!$isArray) {
            $data = $data[0];
        }

        if ($withPagination) {
            $result['data'] = $data;
        } else {
            $result = $data;
        }

        return $result;
    }

    /**
     * 获取对象信息
     *
     * @param  int              $id
     * @return array|Collection     若不存在，返回空数组
     */
    public function get(int $id)
    {
        $this->beforeGet();
        $data = $this->model->get($id);
        $data = $this->afterGet($data);

        return $this->returnArray($data);
    }

    /**
     * 获取对象信息，不存在则抛出异常
     *
     * @param  int              $id
     * @return array|Collection
     */
    public function getOrFail(int $id)
    {
        $data = $this->get($id);

        if ($data === null || (is_array($data) && !$data) || ($data instanceof Collection && $data->isEmpty())) {
            $this->throwNotFoundException();
        }

        return $this->returnArray($data);
    }

    /**
     * 获取多个对象信息
     *
     * @param  array            $ids
     * @return array|Collection
     */
    public function getMultiple(array $ids)
    {
        if (!$ids) {
            $data = [];
        } else {
            $this->beforeGet();
            $data = $this->model->select($ids);
            $data = $this->afterGet($data);
        }

        return $this->returnArray($data);
    }

    /**
     * 抛出对象不存在的异常
     */
    protected function throwNotFoundException(): void
    {
        $exceptions = [
            'answer'   => ErrorConstant::ANSWER_NOT_FOUND,
            'article'  => ErrorConstant::ARTICLE_NOT_FOUND,
            'comment'  => ErrorConstant::COMMENT_NOT_FOUND,
            'image'    => ErrorConstant::IMAGE_NOT_FOUND,
            'question' => ErrorConstant::QUESTION_NOT_FOUND,
            'report'   => ErrorConstant::REPORT_NOT_FOUND,
            'topic'    => ErrorConstant::TOPIC_NOT_FOUND,
            'user'     => ErrorConstant::USER_NOT_FOUND,
        ];

        throw new ApiException($exceptions[$this->model->table]);
    }
}
