<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use ErrorException;
use Slim\App;
use Throwable;

/**
 * 异常和错误处理
 */
class Error
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        error_reporting(E_ALL);
        set_error_handler([$this, 'appError']);
        set_exception_handler([$this, 'appException']);
        register_shutdown_function([$this, 'appShutdown']);
    }

    /**
     * 异常处理
     *
     * @param Throwable $exception
     */
    public function appException(Throwable $exception): void
    {
        new ErrorHandler(
            $this->app->getContainer(),
            $this->app->getResponseFactory(),
            $exception
        );
    }

    /**
     * 错误处理
     *
     * @param int    $errorNo     错误编号
     * @param string $errorString 详细的错误信息
     * @param string $errorFile   出错的文件
     * @param int    $errorLine   出错行号
     */
    public function appError(int $errorNo, string $errorString, string $errorFile = '', int $errorLine = 0): void
    {
        throw new ErrorException($errorString, $errorNo, 0, $errorFile, $errorLine);
    }

    /**
     * 请求结束
     */
    public function appShutdown(): void
    {
        if ($error = error_get_last() !== null) {
            $exception = new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);

            $this->appException($exception);
        }
    }
}
