<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Facade\Model\TopicModel;
use MDClub\Facade\Service\TopicService;

/**
 * 话题验证
 */
class Topic extends Abstracts
{
    protected $attributes = [
        'name' => '话题名称',
        'description' => '话题描述',
        'cover' => '话题封面图片'
    ];

    /**
     * @var string
     */
    protected $lastCover;

    /**
     * 验证话题名称不存在
     *
     * @param int $topicId 排除对该 ID 的验证
     *
     * @return $this
     */
    protected function topicNameNotExist(int $topicId = null): self
    {
        if ($this->trim()->notEmpty()->length(1, 20)->skip()) {
            return $this;
        }

        if ($topicId) {
            TopicModel::where('topic_id[!]', $topicId);
        }

        $has = TopicModel::where('name', $this->value())->has();

        if ($has) {
            $this->setError("话题名称”{$this->value()}“已存在");
        }

        return $this;
    }

    /**
     * 获取更新之前的旧的封面
     *
     * @return string
     */
    public function getLastCover(): string
    {
        return $this->lastCover;
    }

    /**
     * 创建时验证
     *
     * @param  array  $data [name, description, cover]
     * @return array
     */
    public function create(array $data): array
    {
        return $this->data($data)
            ->field('name')->exist()->topicNameNotExist()
            ->field('description')->exist()->trim()->notEmpty()->length(1, 1000)
            ->field('cover')->exist()->uploadedImage(true)
            ->validate();
    }

    /**
     * 更新时验证
     *
     * @param  int   $topicId
     * @param  array $data    [name, description, cover]
     * @return array
     */
    public function update(int $topicId, array $data): array
    {
        $topic = TopicService::getOrFail($topicId);
        $this->lastCover = $topic['cover'];

        return $this->data($data)
            ->field('name')->topicNameNotExist($topicId)
            ->field('description')->trim()->notEmpty()->length(1, 1000)
            ->field('cover')->uploadedImage(true)
            ->validate();
    }
}
