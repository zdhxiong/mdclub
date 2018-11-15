<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Http\Message\UploadedFileInterface;
use App\Abstracts\BrandImageAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;

/**
 * 话题
 *
 * @property-read \App\Model\TopicModel      currentModel
 * @property-read \App\Service\TopicService  currentService
 *
 * Class TopicService
 * @package App\Service
 */
class TopicService extends BrandImageAbstracts
{
    /**
     * @var string 图片类型
     */
    protected $imageType = 'topic-cover';

    /**
     * @var array 图片尺寸
     */
    protected $imageWidths = [
        's' => 360,
        'm' => 720,
        'l' => 1084,
    ];

    /**
     * @var float 图片高宽比
     */
    protected $imageScale = 0.56;


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
    protected function getDefaultImageUrls(): array
    {
        $suffix = $this->isSupportWebp() ? 'webp' : 'jpg';
        $staticUrl = $this->getStaticUrl();
        $data = [];

        foreach (array_keys($this->imageWidths) as $size) {
            $data[$size] = "{$staticUrl}topic-cover/default_{$size}.{$suffix}";
        }

        return $data;
    }

    /**
     * 判断指定话题是否存在
     *
     * @param  int  $topicId
     * @return bool
     */
    public function has(int $topicId): bool
    {
        return $this->topicModel->has($topicId);
    }

    /**
     * 若话题不存在，则抛出异常
     *
     * @param  int  $topicId
     * @return bool
     */
    public function hasOrFail(int $topicId): bool
    {
        if (!$isHas = $this->has($topicId)) {
            throw new ApiException(ErrorConstant::TOPIC_NOT_FOUND);
        }

        return $isHas;
    }

    /**
     * 根据话题ID的数组判断这些话题是否存在
     *
     * @param  array $topicIds 话题ID数组
     * @return array           键名为话题ID，键值为bool值
     */
    public function hasMultiple(array $topicIds): array
    {
        $topicIds = array_unique($topicIds);
        $result = [];

        if (!$topicIds) {
            return $result;
        }

        $existTopicIds = $this->topicModel
            ->where(['topic_id' => $topicIds])
            ->pluck('topic_id');

        foreach ($topicIds as $topicId) {
            $result[$topicId] = in_array($topicId, $existTopicIds);
        }

        return $result;
    }

    /**
     * 获取话题信息
     *
     * @param  int   $topicId
     * @param  bool  $withRelationship
     * @return array
     */
    public function get(int $topicId, bool $withRelationship = false): array
    {
        $topicInfo = $this->topicModel
            ->field($this->getPrivacyFields(), true)
            ->get($topicId);

        if (!$topicInfo) {
            throw new ApiException(ErrorConstant::TOPIC_NOT_FOUND);
        }

        $topicInfo = $this->handle($topicInfo);

        if ($withRelationship) {
            $topicInfo = $this->addRelationship($topicInfo);
        }

        return $topicInfo;
    }

    /**
     * 获取多个话题信息
     *
     * @param  array $topicIds
     * @param  bool  $withRelationship
     * @return array
     */
    public function getMultiple(array $topicIds, bool $withRelationship = false): array
    {
        if (!$topicIds) {
            return [];
        }

        $topics = $this->topicModel
            ->where(['topic_id' => $topicIds])
            ->field($this->getPrivacyFields(), true)
            ->select();

        foreach ($topics as &$topic) {
            $topic = $this->handle($topic);
        }

        if ($withRelationship) {
            $topics = $this->addRelationship($topics);
        }

        return $topics;
    }

    /**
     * 获取话题列表
     *
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(bool $withRelationship = false): array
    {
        $list = $this->topicModel
            ->where($this->getWhere())
            ->order($this->getOrder(['topic_id' => 'ASC']))
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        foreach ($list['data'] as &$topic) {
            $topic = $this->handle($topic);
        }

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
        $this->topicModel->where(['topic_id' => $topicId])->update(['cover' => $fileName]);

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
     * @return bool
     */
    public function update(
        int $topicId,
        string $name = null,
        string $description = null,
        UploadedFileInterface $cover = null
    ): bool {
        $topicInfo = $this->updateValidator($topicId, $name, $description, $cover);

        $data = [];
        !is_null($name) && $data['name'] = $name;
        !is_null($description) && $data['description'] = $description;

        if ($data) {
            $this->topicModel->where(['topic_id' => $topicId])->update($data);
        }

        if (!is_null($cover)) {
            // 先删除旧图片
            $this->deleteImage($topicId, $topicInfo['cover']);

            // 上传并更新图片
            $fileName = $this->uploadImage($topicId, $cover);
            $this->topicModel->where(['topic_id' => $topicId])->update(['cover' => $fileName]);
        }

        return true;
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
    public function batchDelete(array $topicIds): void
    {
        // todo 仅未删除的话题才可以删除
        $this->topicModel->where(['topic_id' => $topicIds])->delete();

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
     * 对数据库中读取的话题数据进行处理
     *
     * @param  array $topicInfo
     * @return array
     */
    public function handle(array $topicInfo): array
    {
        if (!$topicInfo) {
            return $topicInfo;
        }

        if (isset($topicInfo['cover'])) {
            $topicInfo['cover'] = $this->getImageUrls($topicInfo['topic_id'], $topicInfo['cover']);
        }

        return $topicInfo;
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

        $currentUserId = $this->roleService->userId();
        $topicIds = array_unique(array_column($topics, 'topic_id'));
        $followingTopicIds = [];

        if ($currentUserId) {
            if (isset($relationship['is_following'])) {
                $followingTopicIds = $relationship['is_following'] ? $topicIds : [];
            } else {
                $followingTopicIds = $this->followModel->where([
                    'user_id'         => $currentUserId,
                    'followable_id'   => $topicIds,
                    'followable_type' => 'topic',
                ])->pluck('followable_id');
            }
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
