<?php

declare(strict_types=1);

namespace MDClub\Controller;

use MDClub\Helper\Request;
use MDClub\Middleware\NeedManager;

/**
 * 邮件
 *
 * @property-read \MDClub\Library\Email $email
 */
class EmailApi extends Abstracts
{
    /**
     * 发送邮件
     *
     * @uses NeedManager
     * @return array
     */
    public function send(): array
    {
        $email = Request::getBodyParamToArray($this->request, 'email', 100);
        $subject = $this->request->getParsedBody()['subject'] ?? '';
        $content = $this->request->getParsedBody()['content'] ?? '';

        $this->email->send($email, $subject, $content);

        return [
            'email'   => implode(',', $email),
            'subject' => $subject,
            'content' => $content,
        ];
    }
}
