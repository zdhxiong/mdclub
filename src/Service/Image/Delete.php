<?php

declare(strict_types=1);

namespace App\Service\Image;

/**
 * 删除图片
 */
class Delete extends Abstracts
{
    /**
     * 删除图片
     *
     * @param string $hash
     */
    public function delete(string $hash): void
    {
        $image = $this->model->field(['hash', 'create_time'])->get($hash);

        if ($image) {
            $this->model->delete($hash);
            $this->storage->delete(
                $this->getPath($hash, $image['create_time']),
                $this->getSize()
            );
        }
    }

    /**
     * 批量删除图片
     *
     * @param array $hashs
     */
    public function deleteMultiple(array $hashs): void
    {
        if (!$hashs) {
            return;
        }

        $images = $this->model->field(['hash', 'create_time'])->select($hashs);

        if (!$images) {
            return;
        }

        $this->model->delete(array_column($images, 'hash'));

        foreach ($images as $image) {
            $this->storage->delete(
                $this->getPath($image['hash'], $image['create_time']),
                $this->getSize()
            );
        }
    }
}
