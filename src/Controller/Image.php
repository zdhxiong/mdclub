<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 图片
 *
 * Class Image
 * @package App\Controller
 */
class Image extends ControllerAbstracts
{
    /**
     * 获取图片列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        $this->container->roleService->managerIdOrFail();
        $list = $this->container->imageService->getList(true);

        return $this->success($response, $list);
    }

    /**
     * 上传图片
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function upload(Request $request, Response $response): Response
    {
        $userId = $this->container->roleService->userIdOrFail();

        /** @var UploadedFileInterface $file */
        $file = $request->getUploadedFiles()['image'] ?? null;

        $hash = $this->container->imageService->upload($userId, $file);
        $info = $this->container->imageService->getInfo($hash, true);

        return $this->success($response, $info);
    }

    /**
     * 批量删除图片
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function deleteMultiple(Request $request, Response $response): Response
    {
        $this->container->roleService->managerIdOrFail();

        $hashs = ArrayHelper::getQueryParam($request, 'hash', 40);
        $this->container->imageService->deleteMultiple($hashs);

        return $this->success($response);
    }

    /**
     * 获取图片信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $hash
     * @return Response
     */
    public function getOne(Request $request, Response $response, string $hash): Response
    {
        $imageInfo = $this->container->imageService->getInfo($hash, true);

        return $this->success($response, $imageInfo);
    }

    /**
     * 更新图片信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $hash
     * @return Response
     */
    public function updateOne(Request $request, Response $response, string $hash): Response
    {
        $this->container->roleService->managerIdOrFail();

        $filename = $request->getParsedBodyParam('filename');

        $this->container->imageService->updateInfo($hash, ['filename' => $filename]);
        $imageInfo = $this->container->imageService->getInfo($hash, true);

        return $this->success($response, $imageInfo);
    }

    /**
     * 删除图片
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $hash
     * @return Response
     */
    public function deleteOne(Request $request, Response $response, string $hash): Response
    {
        $this->container->roleService->managerIdOrFail();
        $this->container->imageService->delete($hash);

        return $this->success($response);
    }
}
