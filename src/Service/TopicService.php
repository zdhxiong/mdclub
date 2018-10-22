<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Http\Message\UploadedFileInterface;
use App\Abstracts\BrandImageAbstracts;
use App\Interfaces\FollowableInterface;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;

/**
 * 话题
 *
 * Class TopicService
 * @package App\Service
 */
class TopicService extends BrandImageAbstracts implements FollowableInterface
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
    protected function getPrivacyFields(): array
    {
        return $this->roleService->managerId()
            ? []
            : [
                'delete_time',
            ];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    protected function getAllowOrderFields(): array
    {
        return $this->roleService->managerId()
            ? [
                'topic_id',
                'article_count',
                'question_count',
                'follower_count',
                'delete_time',
            ]
            : [];
    }

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    protected function getAllowFilterFields(): array
    {
        return $this->roleService->managerId()
            ? [
                'topic_id',
                'name',
                'delete_time',
            ]
            : [];
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
        $excludeFields = $this->getPrivacyFields();
        $topicInfo = $this->topicModel->field($excludeFields, true)->get($topicId);

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

        $excludeFields = $this->getPrivacyFields();
        $topics = $this->topicModel
            ->where(['topic_id' => $topicIds])
            ->field($excludeFields, true)
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
            ->order($this->getOrder(['follower_count' => 'DESC']))
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

        if ($errors) {
            throw new ValidationException($errors);
        }

        return $topicInfo;
    }

    /**
     * 删除话题
     *
     * @param  int  $topicId
     * @param  bool $softDelete
     * @return bool
     */
    public function delete(int $topicId, bool $softDelete): bool
    {
        $topicInfo = $this->topicModel->force()->get($topicId);
        if (!$topicInfo) {
            throw new ApiException(ErrorConstant::TOPIC_NOT_FOUND);
        }

        if (!$softDelete) {
            $this->topicModel->force();
        }

        $this->topicModel->delete($topicId);

        // 软删除后
        if ($softDelete) {
            // todo 更新话题数量
            // 查询关注了该话题的用户ID，把这些用户的 following_topic_count 置为 null
        }

        // 硬删除后
        else {
            // 删除封面图片
            $this->deleteImage($topicId, $topicInfo['cover']);

            // 删除话题关系
            $this->topicableModel->where(['topic_id' => $topicId])->delete();

            // 删除关注的话题
            $this->followableModel->where([
                'followable_id'   => $topicId,
                'followable_type' => 'topic',
            ])->delete();
        }

        return true;
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
                $followingTopicIds = $this->followableModel->where([
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
