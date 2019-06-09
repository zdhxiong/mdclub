<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 邮件
 */
class Email extends ContainerAbstracts
{
    /**
     * 发送邮件
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function send(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $email = $this->requestService->getParsedBodyParamToArray('email', 100);
        $subject = $request->getParsedBodyParam('subject', '');
        $content = $request->getParsedBodyParam('content', '');

        $this->emailService->send($email, $subject, $content);

        return collect([
            'email'   => implode(',', $email),
            'subject' => $subject,
            'content' => $content,
        ])->render($response);
    }
}
