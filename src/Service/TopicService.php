<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\RequestHelper;
use App\Helper\ValidatorHelper;
use App\Traits\BrandableTraits;
use App\Traits\FollowableTraits;
use App\Traits\BaseTraits;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 话题
 *
 * @property-read \App\Model\TopicModel      currentModel
 *
 * Class TopicService
 * @package App\Service
 */
class TopicService extends ServiceAbstracts
{
    use BaseTraits, FollowableTraits, BrandableTraits;

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
     * 图片尺寸
     *
     * @return array
     */
    protected function getBrandWidths(): array
    {
        return [
            's' => 360,
            'm' => 720,
            'l' => 1084,
        ];
    }

    /**
     * 图片高宽比
     *
     * @return float
     */
    protected function getBrandScale(): float
    {
        return 0.56;
    }

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return ['delete_time'];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    public function getAllowOrderFields(): array
    {
        return ['topic_id', 'follower_count'];
    }

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['topic_id', 'name'];
    }

    /**
     * 获取默认的封面图片地址
     *
     * @return array
     */
    protected function getDefaultBrandUrls(): array
    {
        $suffix = RequestHelper::isSupportWebp($this->request) ? 'webp' : 'jpg';
        $staticUrl = $this->getStaticUrl();
        $data = [];

        foreach (array_keys($this->getBrandWidths()) as $size) {
            $data[$size] = "{$staticUrl}default/topic_cover_{$size}.{$suffix}";
        }

        return $data;
    }

    /**
     * 获取话题列表
     *
     * @param  array $condition
     * [
     *     'is_deleted' => true, // 该值为 true 时，获取已删除的记录；否则获取未删除的记录
     * ]
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(array $condition = [], bool $withRelationship = false): array
    {
        $where = $this->getWhere();
        $defaultOrder = ['topic_id' => 'ASC'];

        if (isset($condition['is_deleted']) && $condition['is_deleted']) {
            $this->topicModel->onlyTrashed();
            $defaultOrder = ['delete_time' => 'DESC'];
        }

        $list = $this->topicModel
            ->where($where)
            ->order($this->getOrder($defaultOrder))
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        $list['data'] = $this->handle($list['data']);

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 创建话题
     *
     * @param  string                $name
     * @param  string                $description
     * @param  UploadedFileInterface $cover
     * @return int
     */
    public function create(
        string $name = null,
        string $description = null,
        UploadedFileInterface $cover = null
    ): int {
        $this->createValidator($name, $description, $cover);

        // 添加话题
        $topicId = (int)$this->topicModel->insert([
            'name' => $name,
            'description' => $description,
        ]);

        // 保存图片
        $fileName = $this->uploadImage($topicId, $cover);

        // 更新图片到话题信息中
        $this->topicModel
            ->where(['topic_id' => $topicId])
            ->update(['cover' => $fileName]);

        return $topicId;
    }

    /**
     * 创建话题前的字段验证
     *
     * @param string                $name
     * @param string                $description
     * @param UploadedFileInterface $cover
     */
    private function createValidator(
        string $name = null,
        string $description = null,
        UploadedFileInterface $cover = null
    ): void {
        $errors = [];

        // 验证名称
        if (!$name) {
            $errors['name'] = '名称不能为空';
        } elseif (!ValidatorHelper::isMax($name, 20)) {
            $errors['name'] = '名称长度不能超过 20 个字符';
        }

        // 验证描述
        if (!$description) {
            $errors['description'] = '描述不能为空';
        } elseif (!ValidatorHelper::isMax($description, 1000)) {
            $errors['description'] = '描述不能超过 1000 个字符';
        }

        // 验证图片
        if (!$cover) {
            $errors['cover'] = '请选择要上传的封面图片';
        } elseif ($coverError = $this->validateImage($cover)) {
            $errors['cover'] = $coverError;
        }

        if (!$errors && $this->topicModel->where(['name' => $name])->has()) {
            $errors['name'] = '该名称已存在';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    /**
     * 更新话题
     *
     * @param  int                   $topicId
     * @param  string                $name
     * @param  string                $description
     * @param  UploadedFileInterface $cover
     */
    public function update(
        int $topicId,
        string $name = null,
        string $description = null,
        UploadedFileInterface $cover = null
    ): void {
        $topicInfo = $this->updateValidator($topicId, $name, $description, $cover);

        $data = [];
        !is_null($name) && $data['name'] = $name;
        !is_null($description) && $data['description'] = $description;

        if ($data) {
            $this->topicModel
                ->where(['topic_id' => $topicId])
                ->update($data);
        }

        if (!is_null($cover)) {
            // 先删除旧图片
            $this->deleteImage($topicId, $topicInfo['cover']);

            // 上传并更新图片
            $fileName = $this->uploadImage($topicId, $cover);
            $this->topicModel
                ->where(['topic_id' => $topicId])
                ->update(['cover' => $fileName]);
        }
    }

    /**
     * 更新话题前的字段验证
     *
     * @param  int                   $topicId
     * @param  string                $name
     * @param  string                $description
     * @param  UploadedFileInterface $cover
     * @return array                 $topicInfo
     */
    private function updateValidator(
        int $topicId,
        string $name = null,
        string $description = null,
        UploadedFileInterface $cover = null
    ): array {
        $topicInfo = $this->topicModel->get($topicId);
        if (!$topicInfo) {
            throw new ApiException(ErrorConstant::TOPIC_NOT_FOUND);
        }

        $errors = [];

        // 验证名称
        if (!is_null($name) && !ValidatorHelper::isMax($name, 20)) {
            $errors['name'] = '名称长度不能超过 20 个字符';
        }

        // 验证描述
        if (!is_null($description) && !ValidatorHelper::isMax($description, 1000)) {
            $errors['description'] = '描述不能超过 1000 个字符';
        }

        // 验证图片
        if (!is_null($cover) && $coverError = $this->validateImage($cover)) {
            $errors['cover'] = $coverError;
        }

        if (
               !$errors
            && !is_null($name)
            && $this->topicModel->where(['name' => $name, 'topic_id[!]' => $topicId])->has()
        ) {
            $errors['name'] = '该名称已存在';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return $topicInfo;
    }

    /**
     * 软删除话题
     *
     * @param  int  $topicId
     */
    public function delete(int $topicId): void
    {
        $rowCount = $this->topicModel->delete($topicId);

        // 话题已被删除
        if (!$rowCount) {
            return;
        }

        // 关注了该话题的用户的 following_topic_count - 1
        $followerIds = $this->followModel
            ->where(['followable_id' => $topicId, 'followable_type' => 'topic'])
            ->pluck('user_id');

        $this->userModel
            ->where(['user_id' => $followerIds])
            ->update(['following_topic_count[-]' => 1]);

        /*$database = $this->container->get(Medoo::class);
        $prefix = $this->container->get('settings')['database']['prefix'];

        // 删除话题关系
        $this->topicableModel->where(['topic_id' => $topicId])->delete();

        // 查询关注了该话题的用户ID，把这些用户的 following_topic_count - 1
        $database->query("UPDATE `{$prefix}user` SET `following_topic_count` = `following_topic_count`-1 WHERE `user_id` IN (SELECT `user_id` FROM `{$prefix}follow` WHERE `followable_id` = {$topicId} AND `followable_type` = 'topic')");

        // 删除关注的话题
        $this->followModel->where([
            'followable_id'   => $topicId,
            'followable_type' => 'topic',
        ])->delete();*/

        // 硬删除后
        /*else {
            // 删除封面图片
            $this->deleteImage($topicId, $topicInfo['cover']);
        }*/
    }

    /**
     * 批量软删除话题
     *
     * @param  array $topicIds
     */
    public function deleteMultiple(array $topicIds): void
    {
        if (!$topicIds) {
            return;
        }

        $topics = $this->topicModel
            ->field(['topic_id'])
            ->select($topicIds);

        if (!$topics) {
            return;
        }

        $topicIds = array_column($topics, 'topic_id');
        $this->topicModel->delete($topicIds);

        // 关注了这些话题的用户的 following_topic_count - 1
        $followerIds = $this->followModel
            ->where(['followable_id' => $topicIds, 'followable_type' => 'topic'])
            ->pluck('user_id');

        $userTopicCount = [];
        foreach ($followerIds as $followerId) {
            isset($userTopicCount[$followerId])
                ? $userTopicCount[$followerId] += 1
                : $userTopicCount[$followerId] = 1;
        }

        foreach ($userTopicCount as $followerId => $count) {
            $this->userModel
                ->where(['user_id' => $followerId])
                ->update(['following_topic_count[-]' => $count]);
        }

        /*$database = $this->container->get(Medoo::class);
        $prefix = $this->container->get('settings')['database']['prefix'];

        // 删除话题关系
        $this->topicableModel->where(['topic_id' => $topicIds])->delete();

        // 查询关注了这些话题的用户ID，把这些用户的 following_topic_count - 1
        foreach ($topicIds as $topicId) {
            $database->query("UPDATE `{$prefix}user` SET `following_topic_count` = `following_topic_count`-1 WHERE `user_id` IN (SELECT `user_id` FROM `{$prefix}follow` WHERE `followable_id` = {$topicId} AND `followable_type` = 'topic')");
        }

        // 删除关注的话题
        $this->followModel->where([
            'followable_id' => $topicIds,
            'followable_type' => 'topic',
        ])->delete();*/
    }

    /**
     * 恢复话题
     *
     * @param int $topicId
     */
    public function restore(int $topicId): void
    {

    }

    /**
     * 批量恢复话题
     *
     * @param array $topicIds
     */
    public function restoreMultiple(array $topicIds): void
    {

    }

    /**
     * 硬删除话题
     *
     * @param int $topicId
     */
    public function destroy(int $topicId): void
    {

    }

    /**
     * 批量硬删除话题
     *
     * @param array $topicIds
     */
    public function destroyMultiple(array $topicIds): void
    {

    }

    /**
     * 对数据库中读取的话题数据进行处理
     *
     * @param  array $topics 话题信息，或多个话题组成的数组
     * @return array
     */
    public function handle(array $topics): array
    {
        if (!$topics) {
            return $topics;
        }

        if (!$isArray = is_array(current($topics))) {
            $topics = [$topics];
        }

        foreach ($topics as &$topic) {
            if (isset($topic['cover'])) {
                $topic['cover'] = $this->getBrandUrls($topic['topic_id'], $topic['cover']);
            }
        }

        if ($isArray) {
            return $topics;
        }

        return $topics[0];
    }

    /**
     * 获取在 relationship 中使用的 topics
     *
     * @param  array  $targetIds  对象ID数组
     * @param  string $targetType 对象类型
     * @return array              键名为对象ID，键值为话题信息组成的二维数组
     */
    public function getInRelationship(array $targetIds, string $targetType): array
    {
        $topics = array_combine($targetIds, array_fill(0, count($targetIds), []));

        $topicsTmp = $this->topicModel
            ->join([
                '[><]topicable' => ['topic_id' => 'topic_id']
            ])
            ->where([
                'topicable.topicable_id' => $targetIds,
                'topicable.topicable_type' => $targetType,
            ])
            ->order(['topicable.create_time' => 'ASC'])
            ->field(['topic.topic_id', 'topic.name', 'topic.cover', 'topicable.topicable_id'])
            ->select();

        foreach ($topicsTmp as $item) {
            $topics[$item['topicable_id']][] = $this->topicService->handle([
                'topic_id' => $item['topic_id'],
                'name'     => $item['name'],
                'cover'    => $item['cover'],
            ]);
        }

        return $topics;
    }

    /**
     * 为话题信息添加 relationship 字段
     * {
     *     is_following: false  登录用户是否已关注该话题
     * }
     *
     * @param  array $topics
     * @param  array $relationship {is_following: false} 若指定了该参数，则不再查询数据库
     * @return array
     */
    public function addRelationship(array $topics, array $relationship = []): array
    {
        if (!$topics) {
            return $topics;
        }

        if (!$isArray = is_array(current($topics))) {
            $topics = [$topics];
        }

        $topicIds = array_unique(array_column($topics, 'topic_id'));

        if (isset($relationship['is_following'])) {
            $followingTopicIds = $relationship['is_following'] ? $topicIds : [];
        } else {
            $followingTopicIds = $this->followService->getIsFollowingInRelationship($topicIds, 'topic');
        }

        foreach ($topics as &$topic) {
            $topic['relationship'] = [
                'is_following' => in_array($topic['topic_id'], $followingTopicIds),
            ];
        }

        if ($isArray) {
            return $topics;
        }

        return $topics[0];
    }
}
