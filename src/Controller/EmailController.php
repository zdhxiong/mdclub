<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 邮件
 *
 * Class EmailController
 * @package App\Controller
 */
class EmailController extends Controller
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

        $email = $request->getParsedBodyParam('email', '');
        $subject = $request->getParsedBodyParam('subject', '');
        $content = $request->getParsedBodyParam('content', '');

        $emailArray = array_filter(array_slice(explode(',', $email), 0, 100));
        $this->emailService->send($emailArray, $subject, $content);

        return $this->success($response, [
            'email'   => implode(',', $emailArray),
            'subject' => $subject,
            'content' => $content,
        ]);
    }
}
