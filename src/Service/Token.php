<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\TokenModel;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Validator\TokenValidator;
use MDClub\Helper\Ip;
use MDClub\Helper\Str;

/**
 * Token 服务
 */
class Token extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getModel(): string
    {
        return \MDClub\Model\Token::class;
    }

    /**
     * 创建 Token
     *
     * @param array $data [name, password, device]
     *
     * @return string
     */
    public function create(array $data): string
    {
        if (!isset($data['device']) || !$data['device']) {
            $data['device'] = Request::getServerParams()['HTTP_USER_AGENT'] ?? '';
        }

        $userId = TokenValidator::create($data);
        $token = Str::guid();

        TokenModel
            ::set('token', $token)
            ->set('user_id', $userId)
            ->set('device', $data['device'])
            ->set('expire_time', Request::time() + Auth::getLifeTime())
            ->insert();

        UserModel
            ::where('user_id', $userId)
            ->set('last_login_time', Request::time())
            ->set('last_login_ip', Ip::getIp())
            ->set('last_login_location', Ip::getLocation())
            ->update();

        return $token;
    }
}
