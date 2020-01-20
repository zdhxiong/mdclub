<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\FollowModel;
use MDClub\Facade\Model\TopicableModel;
use MDClub\Facade\Model\TopicModel;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Validator\TopicValidator;
use MDClub\Helper\Url;
use MDClub\Service\Interfaces\DeletableInterface;
use MDClub\Service\Interfaces\FollowableInterface;
use MDClub\Service\Interfaces\GetableInterface;
use MDClub\Service\Traits\Brandable;
use MDClub\Service\Traits\Deletable;
use MDClub\Service\Traits\Followable;
use MDClub\Service\Traits\Getable;

/**
 * 话题服务
 */
class Topic extends Abstracts implements DeletableInterface, FollowableInterface, GetableInterface
{
    use Brandable;
    use Deletable;
    use Followable;
    use Getable;

    /**
     * @inheritDoc
     */
    protected function getModel(): string
    {
        return \MDClub\Model\Topic::class;
    }

    /**
     * 图片类型
     *
     * @return string
     */
    protected function getBrandType(): string
    {
        return 'topic-cover';
    }

    /**
     * 图片尺寸，宽高比为 0.56
     *
     * @return array
     */
    protected function getBrandSize(): array
    {
        return [
            'small' => [360, 202],
            'middle' => [720, 404],
            'large' => [1080, 606],
        ];
    }

    /**
     * 获取默认的封面图片地址
     *
     * @return array
     */
    protected function getDefaultBrandUrls(): array
    {
        $suffix = Request::isSupportWebp() ? 'webp' : 'jpg';
        $staticPath = Url::staticPath();
        $data['original'] = "{$staticPath}default/topic_cover.{$suffix}";

        foreach (array_keys($this->getBrandSize()) as $size) {
            $data[$size] = "{$staticPath}default/topic_cover_{$size}.{$suffix}";
        }

        return $data;
    }

    /**
     * 创建话题
     *
     * @param array $data [name, description, cover]
     *
     * @return int
     */
    public function create(array $data): int
    {
        $createData = TopicValidator::create($data);

        $topicId = (int)TopicModel
            ::set('name', $createData['name'])
            ->set('description', $createData['description'])
            ->insert();

        $fileName = $this->uploadImage($topicId, $createData['cover']);
        TopicModel
            ::where('topic_id', $topicId)
            ->update('cover', $fileName);

        return $topicId;
    }

    /**
     * 更新话题
     *
     * @param int   $topicId
     * @param array $data
     */
    public function update(int $topicId, array $data): void
    {
        $updateDate = TopicValidator::update($topicId, $data);

        if ($updateDate && (isset($updateDate['name']) || isset($updateDate['description']))) {
            TopicModel::where('topic_id', $topicId);

            if (isset($updateDate['name'])) {
                TopicModel::set('name', $updateDate['name']);
            }

            if (isset($updateDate['description'])) {
                TopicModel::set('description', $updateDate['description']);
            }

            TopicModel::update();
        }

        if (isset($updateDate['cover'])) {
            // 先删除旧图片
            $this->deleteImage($topicId, TopicValidator::getLastCover());

            // 上传并更新图片
            $fileName = $this->uploadImage($topicId, $updateDate['cover']);
            TopicModel
                ::where('topic_id', $topicId)
                ->update('cover', $fileName);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(int $topicId): void
    {
        $this->traitDelete($topicId);
    }

    /**
     * 删除话题后的操作
     *
     * @param array $topics
     */
    public function afterDelete(array $topics): void
    {
        $topicIds = array_column($topics, 'topic_id');

        // 关注了这些话题的用户ID
        $followerIds = FollowModel
            ::where('followable_type', 'topic')
            ->where('followable_id', $topicIds)
            ->pluck('user_id');

        // 每个用户关注的话题数量
        $followingCount = [];
        foreach ($followerIds as $followerId) {
            isset($followingCount[$followerId])
                ? $followingCount[$followerId] += 1
                : $followingCount[$followerId] = 1;
        }

        foreach ($followingCount as $followerId => $count) {
            UserModel::decFollowingTopicCount($followerId, $count);
        }

        FollowModel::deleteByTopicIds($topicIds);
        TopicableModel::deleteByTopicIds($topicIds);

        foreach ($topics as $topic) {
            $this->deleteImage($topic['topic_id'], $topic['cover']);
        }
    }
}
