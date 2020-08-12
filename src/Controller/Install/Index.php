<?php

declare(strict_types=1);

namespace MDClub\Controller\Install;

use Exception;
use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Library\View;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Service\UserAvatarService;
use MDClub\Facade\Validator\UserValidator;
use MDClub\Initializer\App;
use MDClub\Library\Db;
use Psr\Http\Message\ResponseInterface;

/**
 * 安装界面首页
 */
class Index
{
    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/install.php');
    }

    /**
     * 导入数据库
     *
     * @return null
     */
    public function importDatabase()
    {
        $requestBody = Request::getParsedBody();

        try {
            $dbInfo = [
                'DB_HOST'     => $requestBody['db_host'],
                'DB_DATABASE' => $requestBody['db_database'],
                'DB_USERNAME' => $requestBody['db_username'],
                'DB_PASSWORD' => $requestBody['db_password'],
                'DB_PREFIX'   => $requestBody['db_prefix'],
            ];

            // 连接数据库
            $database = new Db($dbInfo);

            // 重新设置 App::$config
            foreach ($dbInfo as $key => $value) {
                App::$config[$key] = $value;
            }
        } catch (Exception $exception) {
            throw new ApiException(
                ApiErrorConstant::SYSTEM_INSTALL_FAILED,
                false,
                $exception->getMessage()
            );
        }

        // 导入数据库文件
        $sqlFile = __DIR__ . '/../../../mdclub.sql';

        if (!file_exists($sqlFile)) {
            throw new ApiException(
                ApiErrorConstant::SYSTEM_INSTALL_FAILED,
                false,
                '未找到根目录下的 mdclub.sql 文件，请检查安装包是否完整'
            );
        }

        $sql = file_get_contents($sqlFile);
        $sql = str_replace(
            ['mc_', "\n"],
            [$requestBody['db_prefix'], ''],
            $sql
        );
        $sqlArr = explode(';', $sql);

        try {
            foreach ($sqlArr as $sqlLine) {
                if ($sqlLine) {
                    $database->query($sqlLine);
                }
            }
        } catch (Exception $exception) {
            throw new ApiException(
                ApiErrorConstant::SYSTEM_INSTALL_FAILED,
                false,
                $exception->getMessage()
            );
        }

        // 创建管理员账号
        $registerData = UserValidator::register([
            'email' => $requestBody['admin_email'],
            'username' => $requestBody['admin_username'],
            'password' => sha1($requestBody['admin_password']),
        ]);

        if (!$requestBody['admin_password']) {
            throw new ValidationException([
                'password' => '密码不能为空',
            ]);
        }

        $userId = (int) UserModel
            ::set('username', $registerData['username'])
            ->set('email', $registerData['email'])
            ->set('password', $registerData['password'])
            ->insert();

        UserAvatarService::delete($userId);

        // 把 user 表的 auto_increment 设为 10000
        $database
            ->query("alter table ${requestBody['db_database']}.${requestBody['db_prefix']}user auto_increment=10000;");

        // 写入 config.php 文件
        $configFile = "<?php

return [
    'DB_HOST' => '${requestBody['db_host']}',
    'DB_DATABASE' => '${requestBody['db_database']}',
    'DB_USERNAME' => '${requestBody['db_username']}',
    'DB_PASSWORD' => '${requestBody['db_password']}',
    'DB_PREFIX' => '${requestBody['db_prefix']}'
];";
        try {
            file_put_contents(__DIR__ . '/../../../config/config.php', $configFile);
        } catch (Exception $exception) {
            throw new ApiException(
                ApiErrorConstant::SYSTEM_INSTALL_FAILED,
                false,
                $exception->getMessage()
            );
        }

        return null;
    }
}
