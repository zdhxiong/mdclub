<?php

declare(strict_types=1);

namespace MDClub\Controller\Install;

use Exception;
use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Library\View;
use MDClub\Facade\Model\UserModel;
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
     * @return array
     */
    public function importDatabase(): array
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
            throw new ApiException(ApiErrorConstant::SYSTEM_INSTALL_FAILED, false, $exception->getMessage());
        }

        // 导入数据库文件
        $sqlFile = __DIR__ . '/../../../mdclub.sql';

        $sql = file_get_contents($sqlFile);
        $sql = str_replace(
            ['mc_', "\n"],
            [$requestBody['db_prefix'], ''],
            $sql
        );
        $sqlArr = explode(';', $sql);

        foreach ($sqlArr as $sqlLine) {
            if ($sqlLine) {
                $database->query($sqlLine);
            }
        }

        // 创建管理员账号
        $registerData = UserValidator::register([
            'email' => $requestBody['admin_email'],
            'username' => $requestBody['admin_username'],
            'password' => sha1($requestBody['admin_password']),
        ]);
        UserModel
            ::set('username', $registerData['username'])
            ->set('email', $registerData['email'])
            ->set('password', sha1($registerData['password']))
            ->insert();

        // 写入 config.php 文件
        $configFile = "<?php

return [
    'DB_HOST' => '${requestBody['db_host']}',
    'DB_DATABASE' => '${requestBody['db_database']}',
    'DB_USERNAME' => '${requestBody['db_username']}',
    'DB_PASSWORD' => '${requestBody['db_password']}',
    'DB_PREFIX' => '${requestBody['db_prefix']}'
];";
        file_put_contents(__DIR__ . '/../../../config/config.php', $configFile);

        return [];
    }
}
