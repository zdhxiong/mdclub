<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 图片
 */
class Image extends ContainerAbstracts
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

        return $this->imageService
            ->fetchCollection()
            ->getList(true)
            ->render($response);
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

        /** @var UploadedFileInterface $file */
        $file = $request->getUploadedFiles()['image'] ?? null;

        $hash = $this->imageService->upload($userId, $file);

        return $this->imageService
            ->fetchCollection()
            ->getInfo($hash, true)
            ->render($response);
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

        $hashs = $this->requestService->getQueryParamToArray('hash', 40);
        $this->imageService->deleteMultiple($hashs);

        return collect()->render($response);
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
        return $this->imageService
            ->fetchCollection()
            ->getInfo($hash, true)
            ->render($response);
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

        return $this->imageService
            ->fetchCollection()
            ->getInfo($hash, true)
            ->render($response);
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

        return collect()->render($response);
    }
}
