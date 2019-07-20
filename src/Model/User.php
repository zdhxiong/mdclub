<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Helper\Ip;
use MDClub\Library\Auth;
use MDClub\Observer\User as UserObserver;
use Psr\Container\ContainerInterface;

/**
 * 用户模型
 *
 * @property-read Auth $auth
 */
class User extends Abstracts
{
    public $table = 'user';
    public $primaryKey = 'user_id';
    protected $timestamps = true;
    protected $observe = UserObserver::class;

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

    public $allowOrderFields = [
        'follower_count',
        'create_time',
    ];

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->allowFilterFields = $this->auth->isManager()
            ? ['user_id', 'username', 'email']
            : ['user_id', 'username'];
    }

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

    /**
     * @inheritDoc
     */
    protected function beforeInsert(array $data): array
    {
        $data = collect($data)->union([
            'avatar' => '',
            'cover' => '',
            'create_ip' => Ip::getIp(),
            'create_location' => Ip::getLocation(),
            'last_login_time' => $this->request->getServerParams()['REQUEST_TIME'] ?? time(),
            'last_login_ip' => Ip::getIp(),
            'last_login_location' => Ip::getLocation(),
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

    /**
     * @inheritDoc
     */
    protected function beforeUpdate(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = $this->passwordHash($data['password']);
        }

        return $data;
    }

    /**
     * 根据 url 参数获取未禁用的用户列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this
            ->where($this->getWhereFromRequest(['disable_time' => 0]))
            ->order($this->getOrderFromRequest(['create_time' => 'ASC']))
            ->field('password', true)
            ->paginate();
    }

    /**
     * 根据 url 参数获取已禁用的用户列表
     *
     * @return array
     */
    public function getDisabled(): array
    {
        return $this
            ->where($this->getWhereFromRequest(['disable_time[>]' => 0]))
            ->order($this->getOrderFromRequest(['disable_time' => 'DESC']))
            ->field('password', true)
            ->paginate();
    }
}
