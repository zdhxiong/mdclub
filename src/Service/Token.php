<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ContainerAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Traits\fetchCollection;

/**
 * Token
 */
class Token extends ContainerAbstracts
{
    use fetchCollection;

    /**
     * token 的有效期，默认为 15天（3600*24*15）
     *
     * @var int
     */
    public $lifeTime = 1296000;

    /**
     * 当前请求的 token. 为 false 时表示 token 不存在
     *
     * @var string
     */
    protected $token;

    /**
     * token 信息数组。为 false 时表示 token 不存在或错误
     *
     * @var array
     */
    protected $tokenInfo;

    /**
     * 获取当前用户的所有的 token 信息（含分页）
     *
     * @return array
     */
    public function getList(): array
    {
        $tokenInfo = $this->getTokenInfoOrFail();
        $user_id = $tokenInfo['user_id'];

        $result = $this->tokenModel
            ->where('user_id', $user_id)
            ->order('expire_time', 'DESC')
            ->paginate();

        return $this->returnArray($result);
    }

    /**
     * 获取当前请求的 token，不存在 token 时返回 false
     *
     * NOTE: 未验证 token 是否有效，若要获取有效的 token，需要调用 getTokenInfo 方法
     *
     * @return string|false
     */
    public function getToken()
    {
        if ($this->token !== null) {
            return $this->token;
        }

        $this->token = $this->request->getServerParam('HTTP_TOKEN')
            ?? $this->request->getCookieParam('token')
            ?? false;

        return $this->token;
    }

    /**
     * 获取当前请求的 token，不存在 token 时抛出异常
     *
     * NOTE: 未验证 token 是否有效，若要获取有效的 token，需要调用 getTokenInfo 方法
     *
     * @return string
     */
    public function getTokenOrFail(): string
    {
        $token = $this->getToken();

        if (!$token) {
            throw new ApiException(ErrorConstant::USER_TOKEN_EMPTY);
        }

        return $token;
    }

    /**
     * 设置当前请求的 token
     *
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
        $this->tokenInfo = null;
    }

    /**
     * 获取当前请求的 token 信息数组，不存在时返回 false
     *
     * @return array|false
     */
    public function getTokenInfo()
    {
        if ($this->tokenInfo !== null) {
            return $this->tokenInfo;
        }

        // token 参数不存在
        $token = $this->getToken();
        if (!$token) {
            $this->tokenInfo = false;
            return false;
        }

        // token 对应的数据不存在
        $tokenInfo = $this->tokenModel->get($token);
        if (!$tokenInfo) {
            $this->tokenInfo = false;
            return false;
        }

        // token 已过期，删除该 token
        $requestTime = $this->requestService->time();
        if ($tokenInfo['expire_time'] < $requestTime) {
            $this->tokenModel->delete($token);
            $this->tokenInfo = false;
            return false;
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
     * 获取当前请求的 token 信息数组，不存在时抛出异常
     *
     * @return array
     */
    public function getTokenInfoOrFail(): array
    {
        $tokenInfo = $this->getTokenInfo();

        if (!$tokenInfo) {
            throw new ApiException(ErrorConstant::USER_TOKEN_FAILED);
        }

        return $tokenInfo;
    }

    /**
     * 删除当前请求对应的 token
     */
    public function deleteToken(): void
    {
        $tokenInfo = $this->getTokenInfo();

        if ($tokenInfo) {
            $this->tokenModel->delete($tokenInfo['token']);
        }
    }

    /**
     * 根据 token 参数删除对应的 token 记录
     *
     * @param  string $token
     */
    public function deleteByToken(string $token): void
    {
        $result = $this->tokenModel->delete($token);

        if (!$result) {
            throw new ApiException(ErrorConstant::USER_TOKEN_NOT_FOUND);
        }
    }
}
