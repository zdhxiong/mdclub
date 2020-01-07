<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Constant\OptionConstant;
use MDClub\Exception\ValidationException;
use MDClub\Facade\Library\Option as OptionLibrary;

/**
 * 配置项验证
 */
class Option extends Abstracts
{
    /**
     * 更新时验证
     *
     * @param array $data
     *
     * @return array
     */
    public function update(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        // 移除不存在的字段
        $data = collect($data)
            ->intersectByKeys(OptionLibrary::getAll())
            ->all();

        if (empty($data)) {
            return [];
        }

        $errors = [];

        foreach ($data as $name => &$value) {
            if (in_array($name, OptionConstant::BOOLEAN_NAMES)) {
                if (!is_bool($value)) {
                    $errors[$name] = '该字段必须是 boolean 类型，例如 true 或 false';
                }

                $value = $value ? 'true' : 'false';
            } elseif (in_array($name, OptionConstant::INTEGER_NAMES)) {
                if (!is_int($value)) {
                    $errors[$name] = '该字段必须是 int 类型';
                }

                $value = (string) $value;
            } elseif (!is_string($value)) {
                $errors[$name] = '该字段必须是字符串';
            } elseif (mb_strlen($value, mb_detect_encoding($value)) > 1000) {
                $errors[$name] = '该字段不能超过 1000 个字符';
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return $data;
    }
}
