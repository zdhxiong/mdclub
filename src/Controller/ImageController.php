<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
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
     * 上传图片
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function upload(Request $request, Response $response): Response
    {
        $this->roleService->userIdOrFail();

        /** @var UploadedFileInterface $avatar */
        $file = $request->getUploadedFiles()['image'] ?? null;

        $hash = $this->imageService->upload($file);
        $info = $this->imageService->getInfo($hash);

        return $this->success($response, $info);
    }
}
