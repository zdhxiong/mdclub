<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Helper\Request;
use MDClub\Model\Token;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 处理用户 Token 有关逻辑，及用户登录状态
 *
 * @property-read ServerRequestInterface $request
 * @property-read Token                  $tokenModel
 */
class Auth
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Token 的有效期，默认为 15天（3600*24*15）
     *
     * @var int
     */
    public $lifeTime = 1296000;

    /**
     * 当前请求的 Token
     *
     * @var string
     */
    protected $token;

    /**
     * Token 信息数组
     *
     * @var array
     */
    protected $tokenInfo;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get(string $name)
    {
        return $this->container->get($name);
    }

    /**
     * 获取当前请求的 Token，不存在 Token 时返回 null
     *
     * NOTE: 未验证 Token 是否有效，若要获取有效的 Token，需要调用 getTokenInfo 方法
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        if ($this->token === false) {
            return null;
        }

        if ($this->token !== null) {
            return $this->token;
        }

        $this->token = $this->request->getServerParams()['HTTP_TOKEN']
            ?? $this->request->getCookieParams()['token']
            ?? false;

        return $this->getToken();
    }

    /**
     * 设置当前请求的 Token
     *
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
        $this->tokenInfo = null;
    }

    /**
     * 获取当前请求的 token 信息数组，不存在或已失效时返回 null
     *
     * @return array|null
     */
    public function getTokenInfo(): ?array
    {
        if ($this->tokenInfo === false) {
            return null;
        }

        if ($this->tokenInfo !== null) {
            return $this->tokenInfo;
        }

        // token 参数不存在
        $token = $this->getToken();
        if (!$token) {
            $this->tokenInfo = false;
            return null;
        }

        // token 对应的数据不存在
        $tokenInfo = $this->tokenModel->get($token);
        if (!$tokenInfo) {
            $this->tokenInfo = false;
            return null;
        }

        // token 已过期，删除该 token
        $requestTime = Request::time($this->request);
        if ($tokenInfo['expire_time'] < $requestTime) {
            $this->tokenModel->delete($token);
            $this->tokenInfo = false;
            return null;
        }

        // token 有效，自动续期
        if ($this->lifeTime - ($tokenInfo['expire_time'] - $requestTime) > 86400) {
            $this->tokenModel
                ->where('token', $token)
                ->update('expire_time', $requestTime + $this->lifeTime);
        }

        $this->tokenInfo = $tokenInfo;

        return $tokenInfo;
    }

    /**
     * 获取当前登录用户的 user_id，未登录则返回 null
     *
     * @return int|null
     */
    public function userId(): ?int
    {
        return $this->getTokenInfo()['user_id'];
    }

    /**
     * 当前登录用户是否是管理员
     *
     * @return bool
     */
    public function isManager(): bool
    {
        return $this->userId() === 1;
    }
}
