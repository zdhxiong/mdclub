<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Service\TokenService;

/**
 * 身份验证 API
 */
class Token extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getService(): string
    {
        return \MDClub\Service\Token::class;
    }

    /**
     * 创建 token（即用户登录）
     *
     * @return array
     */
    public function create(): array
    {
        $requestBody = Request::getParsedBody();
        $token = TokenService::create($requestBody);

        Auth::setToken($token);

        return Auth::getTokenInfo();
    }
}
