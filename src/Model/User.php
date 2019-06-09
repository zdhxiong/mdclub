<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;
use App\Helper\IpHelper;

/**
 * 用户模型
 */
class User extends ModelAbstracts
{
    public $table = 'user';
    public $primaryKey = 'user_id';
    protected $timestamps = true;

    // 被禁用的用户也是真实用户，不作为软删除字段处理

    public $columns = [
        'user_id',
        'username',
        'email',
        'avatar',
        'cover',
        'password',
        'create_ip',
        'create_location',
        'last_login_time',
        'last_login_ip',
        'last_login_location',
        'follower_count',
        'followee_count',
        'following_article_count',
        'following_question_count',
        'following_topic_count',
        'article_count',
        'question_count',
        'answer_count',
        'notification_unread',
        'inbox_unread',
        'headline',
        'bio',
        'blog',
        'company',
        'location',
        'create_time',
        'update_time',
        'disable_time',
    ];

    /**
     * 密码加密方式
     *
     * @param  string      $password
     * @return bool|string
     */
    private function passwordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    protected function beforeInsert(array $data): array
    {
        $data = collect($data)->union([
            'avatar' => '',
            'cover' => '',
            'create_ip' => IpHelper::getIp(),
            'create_location' => IpHelper::getLocation(),
            'last_login_time' => $this->container->request->getServerParams()['REQUEST_TIME'],
            'last_login_ip' => IpHelper::getIp(),
            'last_login_location' => IpHelper::getLocation(),
            'follower_count' => 0,
            'followee_count' => 0,
            'following_article_count' => 0,
            'following_question_count' => 0,
            'following_topic_count' => 0,
            'article_count' => 0,
            'question_count' => 0,
            'answer_count' => 0,
            'notification_unread' => 0,
            'inbox_unread' => 0,
            'headline' => '',
            'bio' => '',
            'blog' => '',
            'company' => '',
            'location' => '',
            'disable_time' => 0,
        ])->all();

        $data['password'] = $this->passwordHash($data['password']);

        return $data;
    }

    protected function beforeUpdate(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = $this->passwordHash($data['password']);
        }

        return $data;
    }
}
