<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Traits\Url;
use Slim\Http\Response;

/**
 * Class ControllerAbstracts
 *
 * @package App\Controller
 */
abstract class ControllerAbstracts extends ContainerAbstracts
{
    use Url;

    /**
     * 返回 API 成功时的 Response
     *
     * @param  Response  $response
     * @param  mixed     $data
     * @return Response
     */
    public function success(Response $response, $data = []): Response
    {
        $result = [
            'code' => 0,
        ];

        if (isset($data['data']) && isset($data['pagination'])) {
            $result['data'] = $data['data'];
            $result['pagination'] = $data['pagination'];
        } else {
            $result['data'] = $data;
        }

        return $response->withJson($result);
    }
}
