<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Simple\PdoCache;
use Symfony\Component\Cache\Simple\MemcachedCache;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use App\Interfaces\FilesystemCacheInterface;
use App\Interfaces\DistributedCacheInterface;

$container = $app->getContainer();

/**
 * whoops：漂亮的错误界面
 *
 * @link https://github.com/filp/whoops
 */
if (APP_DEBUG) {
    /** @var \Slim\Http\Request $request */
    $request = $container->get('request');

    $accept = $request->getHeaderLine('accept');

    if (strpos($accept, 'application/json') > -1) {
        $handler = new \Whoops\Handler\JsonResponseHandler();
        $handler->setJsonApi(true)->addTraceToOutput(true);
    } else {
        $handler = new \Whoops\Handler\PrettyPageHandler();
    }

    $whoops = new \Whoops\Run;
    $whoops->pushHandler($handler)->register();
}

/**
 * 展开控制器方法的第三个参数
 *
 * @link https://www.slimframework.com/docs/v3/objects/router.html#route-strategies
 *
 * @return \Slim\Handlers\Strategies\RequestResponseArgs
 */
$container['foundHandler'] = function () {
    return new \Slim\Handlers\Strategies\RequestResponseArgs();
};

/**
 * 404 错误处理
 *
 * @param  ContainerInterface $container
 * @return \App\Handlers\NotFound
 */
$container['notFoundHandler'] = function (ContainerInterface $container) {
    return new \App\Handlers\NotFound($container);
};

/**
 * 405 错误处理
 *
 * @param  ContainerInterface $container
 * @return \App\Handlers\NotAllowed
 */
$container['notAllowedHandler'] = function (ContainerInterface $container) {
    return new \App\Handlers\NotAllowed($container);
};

/**
 * PHP错误处理
 *
 * @param  ContainerInterface $container
 * @return \App\Handlers\PhpError
 */
$container['phpErrorHandler'] = function (ContainerInterface $container) {
    return new \App\Handlers\PhpError($container);
};

/**
 * 异常处理
 *
 * @param  ContainerInterface $container
 * @return \App\Handlers\Error
 */
$container['errorHandler'] = function (ContainerInterface $container) {
    return new \App\Handlers\Error($container);
};

/**
 * 文件系统缓存
 *
 * @link https://symfony.com/doc/current/components/cache.html
 *
 * @return CacheInterface
 */
$container[FilesystemCacheInterface::class] = function () {
    return new \Symfony\Component\Cache\Simple\FilesystemCache('', 0, __DIR__ . '/../var/cache/');
};

/**
 * 缓存（包括 PDO 缓存和分布式缓存，不包括文件缓存，根据用户在后台的设置而定）
 *
 * @link https://symfony.com/doc/current/components/cache.html
 *
 * @param  ContainerInterface $container
 * @return CacheInterface
 */
$container[CacheInterface::class] = function (ContainerInterface $container) {
    /** @var \App\Service\OptionService $optionService */
    $optionService = $container->get(\App\Service\OptionService::class);
    $option = $optionService->getAll();

    switch ($option['cache_type']) {
        case 'pdo':
            $databaseConfig = $container->get('settings')['database'];
            $pdo = $container->get(\Medoo\Medoo::class)->pdo;

            return new PdoCache($pdo, '', 0, [
                'db_table'        => $databaseConfig['prefix'] . 'cache',
                'db_id_col'       => 'name',
                'db_data_col'     => 'value',
                'db_lifetime_col' => 'life_time',
                'db_time_col'     => 'create_time',
            ]);

        case 'memcached':
            $config = [
                'username' => $option['cache_memcached_username'],
                'password' => $option['cache_memcached_password'],
                'host'     => $option['cache_memcached_host'],
                'port'     => $option['cache_memcached_port'],
            ];

            return new MemcachedCache(MemcachedAdapter::createConnection(
                "memcached://{$config['username']}:{$config['password']}@{$config['host']}:{$config['port']}"
            ));

        case 'redis':
            $config = [
                'username' => $option['cache_redis_username'],
                'password' => $option['cache_redis_password'],
                'host'     => $option['cache_redis_host'],
                'port'     => $option['cache_redis_port'],
            ];

            return new RedisCache(RedisAdapter::createConnection(
                "redis://{$config['username']}:{$config['password']}@{$config['host']}:{$config['port']}"
            ));

        default:
            throw new Exception('不存在指定的缓存类型: ' . $option['cache_type']);
    }
};

/**
 * 分布式缓存
 *
 * @link https://symfony.com/doc/current/components/cache.html
 *
 * @param  ContainerInterface $container
 * @return CacheInterface
 */
$container[DistributedCacheInterface::class] = function (ContainerInterface $container) {
    /** @var \App\Service\OptionService $optionService */
    $optionService = $container->get(\App\Service\OptionService::class);
    $option = $optionService->getAll();

    switch ($option['cache_type']) {
        case 'memcached':
        case 'redis':
            return $container[CacheInterface::class]();

        default:
            throw new Exception('不存在指定的缓存类型：' . $option['cache_type']);
    }
};

/**
 * 日志
 *
 * @link https://seldaek.github.io/monolog/
 *
 * @return LoggerInterface
 */
$container[LoggerInterface::class] = function () {
    $logger = new \Monolog\Logger('mdclub');

    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler(
        __DIR__ . '/../var/logs/' . date('Y-m') . '/' . date('d') . '.log',
        \Monolog\Logger::DEBUG
    ));

    return $logger;
};

/**
 * 文件存储
 *
 * @link http://flysystem.thephpleague.com/docs/
 *
 * @param  ContainerInterface $container
 * @return \League\Flysystem\FilesystemInterface
 */
$container[\League\Flysystem\FilesystemInterface::class] = function (ContainerInterface $container) {
    /** @var \App\Service\OptionService $optionService */
    $optionService = $container->get(\App\Service\OptionService::class);
    $option = $optionService->getAll();

    switch ($option['storage_type']) {
        case 'local':
            $uploadDir = $option['storage_local_dir'];
            if ($uploadDir && !in_array(substr($uploadDir, -1), ['/', '\\'])) {
                $uploadDir .= '/';
            }

            if (!$uploadDir) {
                $uploadDir = __DIR__ . '/../public/upload/';
            }

            $adapter = new \League\Flysystem\Adapter\Local($uploadDir);
            break;

        case 'ftp':
            $adapter = new \League\Flysystem\Adapter\Ftp([
                'host'     => $option['storage_ftp_host'],
                'username' => $option['storage_ftp_username'],
                'password' => $option['storage_ftp_password'],

                /** optional config settings */
                'port'     => $option['storage_ftp_port'],
                'root'     => $option['storage_ftp_root'],
                'passive'  => !!$option['storage_ftp_passive'],
                'ssl'      => !!$option['storage_ftp_ssl'],
                'timeout'  => $option['storage_ftp_timeout'],
            ]);
            break;

        case 'aliyun_oss':
            $adapter = new \Xxtime\Flysystem\Aliyun\OssAdapter([
                'access_id'      => $option['storage_aliyun_oss_access_id'],
                'access_secret'  => $option['storage_aliyun_oss_access_secret'],
                'bucket'         => $option['storage_aliyun_oss_bucket'],
                'endpoint'       => $option['storage_aliyun_oss_endpoint'],
                'timeout'        => 60,
                'connectTimeout' => 5,
            ]);
            break;

        case 'upyun':
            $adapter = new \JellyBool\Flysystem\Upyun\UpyunAdapter(
                $option['storage_upyun_bucket'],
                $option['storage_upyun_operator'],
                $option['storage_upyun_password'],
                $option['storage_upyun_endpoint']
            );

            break;

        case 'qiniu':
            $adapter = new \Overtrue\Flysystem\Qiniu\QiniuAdapter(
                $option['storage_qiniu_access_id'],
                $option['storage_qiniu_access_secret'],
                $option['storage_qiniu_bucket'],
                $option['storage_qiniu_endpoint']
            );
            break;

        default:
            throw new Exception('不存在指定的存储类型：' . $option['storage_type']);
    }

    return new \League\Flysystem\Filesystem($adapter, [
        'visibility' => \League\Flysystem\AdapterInterface::VISIBILITY_PUBLIC,
    ]);
};

/**
 * PHP 模板
 *
 * @param  ContainerInterface      $container
 * @return \Slim\Views\PhpRenderer
 */
$container[\Slim\Views\PhpRenderer::class] = function (ContainerInterface $container) {
    /** @var \App\Service\OptionService $optionService */
    $optionService = $container->get(\App\Service\OptionService::class);

    $theme = $optionService->getAll()['theme'];

    // 继承 PhpRenderer，使其具有主题功能
    return new class($theme, __DIR__ . '/../templates/') extends \Slim\Views\PhpRenderer {
        // 用户设置的主题
        protected $theme;

        // 默认主题
        protected $defaultTheme = 'default';

        public function __construct(string $theme, string $templatePath)
        {
            $this->theme = $theme;
            $this->templatePath = rtrim($templatePath, '/\\') . '/';
            $this->attributes = [];
        }

        public function fetch($template, array $data = [])
        {
            $theme = is_file($this->templatePath . $this->theme . $template) ? $this->theme : $this->defaultTheme;

            return parent::fetch('/' . $theme . $template, $data);
        }
    };
};

/**
 * 数据库操作工具
 *
 * @link https://medoo.lvtao.net/1.2/doc.php
 *
 * @param  ContainerInterface $container
 * @return \Medoo\Medoo
 */
$container[\Medoo\Medoo::class] = function (ContainerInterface $container) {
    $config = $container->get('settings')['database'];

    return new \Medoo\Medoo([
        'database_type' => $config['driver'],
        'server'        => $config['host'],
        'database_name' => $config['database'],
        'username'      => $config['username'],
        'password'      => $config['password'],
        'charset'       => $config['charset'],
        'port'          => $config['port'],
        'prefix'        => $config['prefix'],
        'logging'       => APP_DEBUG,
        'option'        => [
            PDO::ATTR_CASE               => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS       => PDO::NULL_NATURAL,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ],
    ]);
};

/**
 * 应用模块
 */

$modules = [
    \App\Model\AnswerModel::class,
    \App\Model\ArticleModel::class,
    \App\Model\CommentModel::class,
    \App\Model\FollowModel::class,
    \App\Model\ImageModel::class,
    \App\Model\InboxModel::class,
    \App\Model\NotificationModel::class,
    \App\Model\OptionModel::class,
    \App\Model\QuestionModel::class,
    \App\Model\ReportModel::class,
    \App\Model\TokenModel::class,
    \App\Model\TopicableModel::class,
    \App\Model\TopicModel::class,
    \App\Model\UserModel::class,
    \App\Model\VoteModel::class,

    \App\Service\AnswerService::class,
    \App\Service\ArticleService::class,
    \App\Service\CaptchaService::class,
    \App\Service\CommentService::class,
    \App\Service\EmailService::class,
    \App\Service\FollowService::class,
    \App\Service\ImageService::class,
    \App\Service\InboxService::class,
    \App\Service\NotificationService::class,
    \App\Service\OptionService::class,
    \App\Service\QuestionService::class,
    \App\Service\ReportService::class,
    \App\Service\RoleService::class,
    \App\Service\ThrottleService::class,
    \App\Service\TokenService::class,
    \App\Service\TopicService::class,
    \App\Service\UserAvatarService::class,
    \App\Service\UserCoverService::class,
    \App\Service\UserLoginService::class,
    \App\Service\UserPasswordResetService::class,
    \App\Service\UserRegisterService::class,
    \App\Service\UserService::class,
    \App\Service\VoteService::class,
];

foreach ($modules as $class) {
    $container[$class] = function (ContainerInterface $container) use ($class) {
        return new $class($container);
    };
}
