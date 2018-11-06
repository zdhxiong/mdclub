<?php

declare(strict_types=1);

namespace App\Helper;

/**
 * 数组相关方法
 *
 * Class ArrayHelper
 * @package app\common\helper
 */
class ArrayHelper
{

    /**
     * 为 data 数组添加默认值
     *
     * @param  array $array   数组
     * @param  array $default 默认值
     * @return array          添加默认值后的数组
     */
    public static function fill(array $array, array $default): array
    {
        foreach ($default as $key => $value) {
            !isset($array[$key]) && $array[$key] = $value;
        }

        return $array;
    }

    /**
     * 过滤掉 $array 中键名不在 $keys 数组中的元素
     *
     * @param  array $array 键值对数组
     * @param  array $keys  字段数组
     * @return array        过滤后的数组
     */
    public static function filter(array $array, array $keys): array
    {
        foreach ($array as $key => $value) {
            if (!in_array($key, $keys)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * 过滤掉 $array 中键名在 $keys 数组中的元素
     *
     * @param  array $array 键值对数组
     * @param  array $keys  字段数组
     * @return array        过滤后的数组
     */
    public static function exclude(array $array, array $keys): array
    {
        foreach ($array as $key => $value) {
            if (in_array($key, $keys)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * 判断数组 $array 的键名是否包含了 $need 中的所有元素
     *
     * @param  array $array 键值对数组
     * @param  array $need  必须包含的字段数组
     * @return bool
     */
    public static function isInclude(array $array, array $need): bool
    {
        foreach ($array as $key => $value) {
            if (!in_array($key, $need)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 向数组的最后追加一个或多个元素
     *
     * @param  array       $array 向该数组追加
     * @param  array|mixed $value 追加的元素，或元素的数组
     * @return array
     */
    public static function push($array, $value): array
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $item) {
            if (!in_array($item, $array)) {
                array_push($array, $item);
            }
        }

        return $array;
    }

    /**
     * 从数组中删除值为 $value 或值在 $value 数组中的值
     *
     * @param  array       $array
     * @param  array|mixed $value
     * @return array
     */
    public static function remove($array, $value): array
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($array as $k => $v) {
            if (in_array($v, $value)) {
                unset($array[$k]);
            }
        }

        return $array;
    }

    /**
     * 根据两个数组的其中一个键值合并数组
     *
     * @param  array  $array1   第一个数组
     * @param  array  $array2   第二个数组
     * @param  string $field1   第一个数组的字段名
     * @param  string $field2   第二个数组的字段名
     * @param  string $sub_name 子数组字段名。若存在，则把第二个数组作为第一个数组的子数组
     * @param  bool   $multi    子数组是否保存成多维数组
     * @return array
     */
    public static function merge(
        array $array1,
        array $array2,
        string $field1,
        string $field2,
        string $sub_name = '',
        bool $multi = false): array {
        $ret = [];
        $array3 = [];

        //使用数组下标的办法
        foreach ($array2 as $value) {
            if ($multi) {
                $array3[$value[$field2]][] = $value;
            } else {
                $array3[$value[$field2]] = $value;
            }
        }

        foreach ($array1 as $value) {
            if ($sub_name) {
                $sub_array[$sub_name] = $array3[$value[$field1]];
                $ret[] = array_merge($sub_array, $value);
            } else {
                $ret[] = array_merge($array3[$value[$field1]], $value);
            }
        }
        return $ret;
    }
}
