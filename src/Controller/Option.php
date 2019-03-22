<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 系统设置
 *
 * Class OptionController
 * @package App\Controller
 */
class Option extends ControllerAbstracts
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
        $data = $this->container->optionService->getMultiple();

        if (!$this->container->roleService->managerId()) {
            $data = ArrayHelper::filter($data, $this->container->optionService->publicNames);
        }

        return $this->success($response, $data);
    }

    /**
     * 更新设置
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function updateMultiple(Request $request, Response $response): Response
    {
        $this->container->roleService->managerIdOrFail();

        $this->container->optionService->setMultiple($request->getParsedBody());
        $data = $this->container->optionService->getMultiple();

        return $this->success($response, $data);
    }
}
