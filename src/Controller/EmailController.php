<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 邮件
 *
 * Class EmailController
 * @package App\Controller
 */
class EmailController extends ControllerAbstracts
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

        $email = ArrayHelper::getParsedBodyParam($request, 'email', 100);
        $subject = $request->getParsedBodyParam('subject', '');
        $content = $request->getParsedBodyParam('content', '');

        $this->emailService->send($email, $subject, $content);

        return $this->success($response, [
            'email'   => implode(',', $email),
            'subject' => $subject,
            'content' => $content,
        ]);
    }
}
