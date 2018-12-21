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
 * Class ImageController
 * @package App\Controller
 */
class ImageController extends ControllerAbstracts
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
        $this->roleService->managerIdOrFail();
        $list = $this->imageService->getList(true);

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
        $userId = $this->roleService->userIdOrFail();

        /** @var UploadedFileInterface $avatar */
        $file = $request->getUploadedFiles()['image'] ?? null;

        $hash = $this->imageService->upload($userId, $file);
        $info = $this->imageService->getInfo($hash, true);

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
        $this->roleService->managerIdOrFail();

        $hashs = ArrayHelper::getQueryParam($request, 'hash', 40);
        $this->imageService->deleteMultiple($hashs);

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
        $imageInfo = $this->imageService->getInfo($hash, true);

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
        $this->roleService->managerIdOrFail();

        $filename = $request->getParsedBodyParam('filename');

        $this->imageService->updateInfo($hash, ['filename' => $filename]);
        $imageInfo = $this->imageService->getInfo($hash, true);

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
        $this->roleService->managerIdOrFail();
        $this->imageService->delete($hash);

        return $this->success($response);
    }
}
