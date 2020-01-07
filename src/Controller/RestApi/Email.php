<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Facade\Library\Email as EmailLibrary;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Validator\EmailValidator;

/**
 * 邮件 API
 */
class Email
{
    /**
     * 发送邮件
     *
     * @return array
     */
    public function send(): array
    {
        $parsedBody = EmailValidator::send(Request::getParsedBody());

        EmailLibrary::send($parsedBody['email'], $parsedBody['subject'], $parsedBody['content']);

        return [
            'email'   => $parsedBody['email'],
            'subject' => $parsedBody['subject'],
            'content' => $parsedBody['content'],
        ];
    }
}
