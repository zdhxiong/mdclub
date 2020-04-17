## 安装步骤

可写权限的目录：

var: 缓存，临时文件
public/sitemap: 自动生成 sitemap 文件

1. 执行 `composer install --no-dev` 安装依赖
2. 复制 `config.example.php`，并重命名为 `config.php`，修改其中的数据库配置信息

## PSR 接口

### psr/container

使用 [silexphp/Pimple](https://github.com/silexphp/Pimple) 实现该接口

### psr/event-dispatcher

使用 [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher) 实现该接口


### psr/http-client

使用 [kriswallsmith/buzz](https://github.com/kriswallsmith/Buzz) 实现该接口

### psr/http-factory

使用 [slim/psr7](https://github.com/slimphp/Slim-Psr7) 实现该接口

### psr/http-message

使用 [slim/psr7](https://github.com/slimphp/Slim-Psr7) 实现该接口

### psr/http-server-handler

### psr/http-server-middleware

### psr/log

使用 [monolog/monolog](https://github.com/Seldaek/monolog) 实现该接口

### psr/simple-cache

使用 [symfony/cache](https://github.com/symfony/cache) 实现该接口
