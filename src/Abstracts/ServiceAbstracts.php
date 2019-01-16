<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Traits\Url;
use App\Helper\ArrayHelper;

/**
 * Class ServiceAbstracts
 *
 * @package App\Service
 */
abstract class ServiceAbstracts extends ContainerAbstracts
{
    use Url;

    /**
     * 当前 Service 对应的 Model 实例
     */
    protected $currentModel;

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
     * ServiceAbstracts constructor.
     *
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container);

        $modelName = lcfirst(substr(get_class($this), 12)) . 'Model';
        if ($this->container->has($modelName)) {
            $this->currentModel = $this->container->get($modelName);
        }
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
    protected function getOrder(array $defaultOrder = [], array $allowOrderFields = null): array
    {
        $result = [];
        $order = $this->container->request->getQueryParam('order');

        if (is_null($allowOrderFields)) {
            $allowOrderFields = $this->getAllowOrderFields();
        }

        if ($order) {
            if (strpos($order, '-') === 0) {
                $result[substr($order, 1)] = 'DESC';
            } else {
                $result[$order] = 'ASC';
            }

            $result = ArrayHelper::filter($result, $allowOrderFields);
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
    protected function getWhere(array $defaultFilter = [], array $allowFilterFields = null): array
    {
        if (is_null($allowFilterFields)) {
            $allowFilterFields = $this->getAllowFilterFields();
        }

        $result = $this->container->request->getQueryParams();
        $result = ArrayHelper::filter($result, $allowFilterFields);
        $result = array_merge($result, $defaultFilter);

        return $result;
    }
}
