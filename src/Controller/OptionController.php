<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\ArrayHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 系统设置
 *
 * Class OptionController
 * @package App\Controller
 */
class OptionController extends Controller
{
    /**
     * 获取所有设置项
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function getAll(Request $request, Response $response): Response
    {
        $data = $this->optionService->getAll();

        if (!$this->roleService->managerId()) {
            $data = ArrayHelper::filter($data, $this->optionService->publicNames);
        }

        return $this->success($response, $data);
    }

    /**
     * 更新设置
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function setMultiple(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $this->optionService->setMultiple($request->getParsedBody());
        $data = $this->optionService->getAll();

        return $this->success($response, $data);
    }
}
