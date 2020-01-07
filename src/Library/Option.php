<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Exception\SystemException;
use MDClub\Constant\OptionConstant;
use MDClub\Facade\Library\Auth as AuthFacade;
use MDClub\Facade\Model\OptionModel;
use MDClub\Facade\Validator\OptionValidator;

/**
 * 配置项
 */
class Option
{
    /**
     * 保存的设置值
     *
     * @var array
     */
    protected $options;

    /**
     * 是否仅获取用户有权限访问的字段
     *
     * @var bool
     */
    protected $onlyAuthorized = false;

    /**
     * 是否仅获取用户有权限访问的字段
     *
     * @return Option
     */
    public function onlyAuthorized(): Option
    {
        $this->onlyAuthorized = true;

        return $this;
    }

    /**
     * 获取指定配置项的值
     *
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        $options = $this->getAll();

        if (!isset($options[$name])) {
            throw new SystemException('不存在指定的设置项：' . $name);
        }

        return $options[$name];
    }

    /**
     * 获取所有配置项的值
     *
     * @return array
     */
    public function getAll(): array
    {
        if ($this->options === null) {
            $options = OptionModel::pluck('value', 'name');

            foreach ($options as $name => &$value) {
                if (in_array($name, OptionConstant::BOOLEAN_NAMES)) {
                    $value = $value === 'true';
                } elseif (in_array($name, OptionConstant::INTEGER_NAMES)) {
                    $value = (int)$value;
                }
            }

            $this->options = $options;
        }

        $options = $this->options;

        if ($this->onlyAuthorized && AuthFacade::isNotManager()) {
            $this->onlyAuthorized = false;
            $options = collect($options)->only(OptionConstant::PUBLIC_NAMES)->all();
        }

        return $options;
    }

    /**
     * 更新多个设置
     *
     * @param array $data
     */
    public function setMultiple(array $data): void
    {
        $data = OptionValidator::update($data);

        if (!$data) {
            return;
        }

        foreach ($data as $name => $value) {
            OptionModel::where('name', $name)->update('value', $value);
        }

        $this->options = null;
    }
}
