<?php

declare(strict_types=1);

namespace MDClub\Service\Topic;

/**
 * 删除话题
 */
class Delete extends Abstracts
{
    /**
     * 软删除话题
     *
     * @param  int  $topicId
     */
    public function delete(int $topicId): void
    {
        $rowCount = $this->model->delete($topicId);

        // 话题已被删除
        if (!$rowCount) {
            return;
        }

        // 关注了该话题的用户的 following_topic_count - 1
        $followerIds = $this->followModel
            ->where('followable_type', 'topic')
            ->where('followable_id', $topicId)
            ->pluck('user_id');

        if ($followerIds) {
            $this->userModel
                ->where('user_id', $followerIds)
                ->dec('following_topic_count')
                ->update();
        }

        /*$database = $this->get(Medoo::class);
        $prefix = $this->get('settings')['database']['prefix'];

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

        $topics = $this->model
            ->field(['topic_id'])
            ->select($topicIds);

        if (!$topics) {
            return;
        }

        $topicIds = array_column($topics, 'topic_id');
        $this->model->delete($topicIds);

        // 关注了这些话题的用户的 following_topic_count - 1
        $followerIds = $this->followModel
            ->where('followable_type', 'topic')
            ->where('followable_id', $topicIds)
            ->pluck('user_id');

        $userTopicCount = [];
        foreach ($followerIds as $followerId) {
            isset($userTopicCount[$followerId])
                ? $userTopicCount[$followerId] += 1
                : $userTopicCount[$followerId] = 1;
        }

        foreach ($userTopicCount as $followerId => $count) {
            $this->userModel
                ->where('user_id', $followerId)
                ->dec('following_topic_count', $count)
                ->update();
        }

        /*$database = $this->get(Medoo::class);
        $prefix = $this->get('settings')['database']['prefix'];

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
}
