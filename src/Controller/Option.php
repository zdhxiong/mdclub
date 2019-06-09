<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 系统设置
 */
class Option extends ContainerAbstracts
{
    /**
     * 获取所有设置项
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function getAll(Request $request, Response $response): Response
    {
        return $this->optionService
            ->fetchCollection()
            ->onlyAuthorized()
            ->getMultiple()
            ->render($response);
    }

    /**
     * 更新设置，仅管理员可操作
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function updateMultiple(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();
        $this->optionService->setMultiple($request->getParsedBody());

        return $this->optionService
            ->fetchCollection()
            ->getMultiple()
            ->render($response);
    }
}
