<?php

declare(strict_types=1);

namespace MDClub\Service\Image;

/**
 * 图片删除
 */
class Delete extends Abstracts
{
    /**
     * 删除图片
     *
     * @param string $key
     */
    public function delete(string $key): void
    {
        $image = $this->model->field(['key', 'create_time'])->get($key);

        if ($image) {
            $this->model->delete($key);

            $this->storage->delete(
                $this->getPath($key, $image['create_time']),
                $this->getSize()
            );
        }
    }

    /**
     * 批量删除图片
     *
     * @param array $keys
     */
    public function deleteMultiple(array $keys): void
    {
        if (!$keys) {
            return;
        }

        $images = $this->model->field(['key', 'create_time'])->select($keys);

        if (!$images) {
            return;
        }

        $this->model->delete(array_column($images, 'key'));

        foreach ($images as $image) {
            $this->storage->delete(
                $this->getPath($image['key'], $image['create_time']),
                $this->getSize()
            );
        }
    }
}
