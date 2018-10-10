<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 图片
 *
 * Class ImageController
 * @package App\Controller
 */
class ImageController extends Controller
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
        return $response;
    }

    /**
     * 更新图片信息
     *
     * @param Request $request
     * @param Response $response
     * @param int $image_id
     *
     * @return Response
     */
    public function update(Request $request, Response $response, int $image_id): Response
    {
        return $response;
    }
}
