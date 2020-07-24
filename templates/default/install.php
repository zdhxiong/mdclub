<?php

use MDClub\Helper\Str;

// 运行环境检查通过
$GLOBALS['environmentSuccess'] = true;

/**
 * @param  bool   $success  是否校验成功
 * @param  string $text     描述文本
 * @param  null   $failText 校验不成功时的描述文本
 * @param  bool   $must     是否必须通过校验才能安装
 * @return string
 */
function checkResult($success, string $text, $failText = null, bool $must = true)
{
    if ($success) {
        return '<i class="mdui-icon material-icons success">check</i>' . $text;
    } else {
        if ($must) {
            $GLOBALS['environmentSuccess'] = false;
        }
        return '<i class="mdui-icon material-icons error">close</i>' . $failText ?: $text;
    }
}

?>
<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mdui@1.0.0/dist/css/mdui.min.css"/>
    <style>
        body {
            background-color: #eee;
            display: flex;
            justify-content: center;
        }

        /* 加载状态遮罩 */
        .mc-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999999;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, .65);
            opacity: 0;
            transition: opacity .2s ease;
            will-change: opacity;
        }

        .mc-loading-overlay-show {
            opacity: 1;
        }

        @media screen and (prefers-color-scheme: dark) {
            .mc-loading-overlay {
                background: rgba(255, 255, 255, .16);
            }
        }

        /* 设置表格样式 */
        .mdui-table-fluid,
        .mdui-table {
            box-shadow: none !important;
            border: none !important;
            margin-top: 8px !important;
            background-color: transparent !important;
        }

        .mdui-table-fluid {
            width: auto;
            margin-left: -8px;
            margin-right: -8px;
        }

        .mdui-table th,
        .mdui-table td {
            padding-top: 8px;
            padding-bottom: 8px;
            padding-right: 0;
        }

        .mdui-table td {
            border-bottom: none;
        }

        .mdui-table th:first-child,
        .mdui-table td:first-child {
            padding-left: 8px;
        }

        .mdui-table th:last-child,
        .mdui-table td:last-child {
            padding-right: 8px;
        }

        @media screen and (max-width: 600px) {
            .mdui-table-fluid {
                margin-left: -16px;
                margin-right: -16px;
            }

            .mdui-table-fluid .mdui-table {
                padding: 0 8px;
            }
        }

        /* 文本框 */
        @media screen and (prefers-color-scheme: dark) {
            .mdui-textfield-focus .mdui-textfield-label {
                color: #8ab4f8 !important;
            }

            .mdui-textfield-focus .mdui-textfield-input {
                border-bottom-color: #8ab4f8 !important;
                box-shadow: 0 1px 0 0 #8ab4f8 !important;
            }
        }

        /* 环境满足与否的 */
        .success,
        .error {
            font-size: 20px;
            margin-right: 4px;
        }

        .success {
            color: #4CAF50;
        }

        .error {
            color: #F44336;
        }

        /* 底部按钮 */
        .actions {
            display: flex;
            align-items: center;
            margin-top: 24px;
            height: 36px;
        }

        .next-step {
            background-color: #1a73e8;
            color: #fff;
            border-radius: 4px;
        }

        .next-step:hover {
            background-color: rgba(26, 115, 232, .87);
        }

        .next-step:active {
            background-color: rgba(26, 115, 232, .72) !important;
        }

        .next-step-disabled {
            color: #F44336;
        }

        @media screen and (prefers-color-scheme: dark) {
            .next-step {
                background-color: #8ab4f8;
            }

            .next-step:hover {
                background-color: rgba(138, 180, 248, .87) !important;
            }

            .next-step:active {
                background-color: rgba(138, 180, 248, .72) !important;
            }
        }

        /* 显示 tooltip 的小图标 */
        .notice {
            font-size: 18px;
            margin-left: 4px;
        }

        .main {
            background-color: #fff;
            border-radius: 4px;
            width: 100%;
            max-width: 640px;
            margin: 16px auto;
        }

        @media screen and (max-width: 600px) {
            .main {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
            }
        }

        @media screen and (prefers-color-scheme: dark) {
            .main {
                background-color: #424242;
            }
        }

        @media screen and (max-width: 600px) and (prefers-color-scheme: dark) {
            .main {
                background-color: #303030;
            }
        }

        /* 步进器 */
        .steppers {
            display: flex;
            align-items: center;
            height: 72px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .2);
        }

        .steppers .item {
            display: flex;
            align-items: center;
            height: 100%;
            padding: 0 8px;
        }

        .steppers .item:first-child {
            padding-left: 24px;
        }

        .steppers .item:last-child {
            padding-right: 24px;
        }

        .steppers .divider {
            display: flex;
            flex-grow: 1;
            height: 1px;
            height: .5px;
            background-color: #E0E0E0;
        }

        .steppers i {
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            font-size: 12px;
            font-style: normal;
            border-radius: 50%;
            color: #fff;
            background-color: rgba(0, 0, 0, .38);
        }

        .steppers .check {
            font-size: 16px;
            display: none;
        }

        .steppers label {
            font-size: 14px;
            color: rgba(0, 0, 0, .38);
            padding-left: 8px;
        }

        .steppers .active i,
        .steppers .done i {
            background-color: #1a73e8;
        }

        .steppers .done .number {
            display: none;
        }

        .steppers .done .check {
            display: block;
        }

        .steppers .active label,
        .steppers .done label {
            color: rgba(0, 0, 0, .87);
        }

        .steppers .active label {
            font-weight: bold;
        }

        @media screen and (max-width: 600px) {
            .steppers .item:first-child {
                padding-left: 16px;
            }

            .steppers .item:last-child {
                padding-right: 16px;
            }
        }

        @media screen and (prefers-color-scheme: dark) {
            .steppers {
                box-shadow: 0 1px 2px 0 rgba(255, 255, 255, .16);
            }

            .steppers .divider {
                background-color: #606060;
            }

            .steppers i {
                background-color: rgba(255, 255, 255, .54);
            }

            .steppers label {
                color: rgba(255, 255, 255, .7);
            }

            .steppers .active i,
            .steppers .done i {
                background-color: #8ab4f8;
            }

            .steppers .active label,
            .steppers .done label {
                color: rgba(255, 255, 255, 1);
            }
        }

        /* 正文部分 */
        .section + .section {
            margin-top: 36px;
        }

        .content {
            padding: 24px;
        }

        @media screen and (max-width: 600px) {
            .content {
                padding: 16px;
            }
        }

        .database,
        .complete {
            display: none;
        }

        /* 环境检查 */
        @media screen and (max-width: 600px) {
            .environment th:nth-child(3),
            .environment td:nth-child(3) {
                display: none;
            }
        }

        /* 导入数据库，创建管理员 */
        .database .form {
            margin-top: 16px;
        }

        /* 安装完成 */
        .complete {
            padding-top: 48px;
            padding-bottom: 48px;
            height: calc(100vh - 72px);
            box-sizing: border-box;
        }

        .complete .mdui-typo-headline {
            text-align: center;
            margin-bottom: 40px;
        }

        .complete .mdui-typo-body-2 {
            line-height: 32px;
        }
    </style>
    <title>安装 MDClub</title>
</head>
<body class="mdui-theme-accent-blue mdui-theme-layout-auto">
<div class="main mdui-shadow-2">
    <div class="steppers">
        <div class="item active item-1">
            <i class="number">1</i>
            <i class="mdui-icon material-icons check">check</i>
            <label>环境检查</label>
        </div>
        <div class="divider"></div>
        <div class="item item-2">
            <i class="number">2</i>
            <i class="mdui-icon material-icons check">check</i>
            <label>创建数据库</label>
        </div>
        <div class="divider"></div>
        <div class="item item-3">
            <i class="number">3</i>
            <i class="mdui-icon material-icons check">check</i>
            <label>完成</label>
        </div>
    </div>
    <div class="container">
        <div class="content environment">
            <div class="section">
                <div class="title mdui-typo-title">运行环境检查</div>
                <div class="mdui-table-fluid">
                    <table class="mdui-table mdui-table-hoverable">
                        <thead>
                        <tr>
                            <th width="86">项目</th>
                            <th>所需配置</th>
                            <th>推荐配置</th>
                            <th>当前服务器</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>操作系统</td>
                            <td>不限制</td>
                            <td>类 Unix</td>
                            <td>
                                <?= checkResult(true, php_uname('s')) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>PHP 版本</td>
                            <td>7.2 ~ 7.4</td>
                            <td>7.4</td>
                            <td>
                                <?= checkResult(
                                    version_compare(PHP_VERSION, '7.2') > 0 && version_compare(PHP_VERSION, '7.5') < 0,
                                    PHP_VERSION
                                ) ?>
                            </td>
                        </tr>
                        <tr>
<?php
$uploadMinFilesize = '256KB';
$uploadMaxFilesize = ini_get('upload_max_filesize');
?>
                            <td>附件上传</td>
                            <td><?= $uploadMinFilesize ?></td>
                            <td>5M</td>
                            <td>
                                <?= checkResult(
                                    Str::memoryCompare($uploadMinFilesize, $uploadMaxFilesize) <= 0,
                                    $uploadMaxFilesize
                                ) ?>
                            </td>
                        </tr>
                        <tr>
<?php
$diskFreeSpaceMin = '100M';
$diskFreeSpace = Str::memoryFormat((int) disk_free_space("."));
?>
                            <td>磁盘空间</td>
                            <td><?= $diskFreeSpaceMin ?></td>
                            <td>10GB</td>
                            <td>
                                <?= checkResult(
                                    Str::memoryCompare($diskFreeSpaceMin, $diskFreeSpace) <= 0,
                                    $diskFreeSpace
                                ) ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="section">
                <div class="title mdui-typo-title">目录权限检查</div>
                <div class="mdui-table-fluid">
                    <table class="mdui-table mdui-table-hoverable">
                        <thead>
                        <tr>
                            <th>目录</th>
                            <th>所需状态</th>
                            <th>当前状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>./config</td>
                            <td>可写</td>
                            <td>
                                <?= checkResult(is_writable(__DIR__ . '/../../config'), '可写', '不可写') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>./var</td>
                            <td>可写</td>
                            <td>
                                <?= checkResult(is_writable(__DIR__ . '/../../var'), '可写', '不可写') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>./public/sitemap</td>
                            <td>可写</td>
                            <td>
                                <?= checkResult(is_writable(__DIR__ . '/../../public/sitemap'), '可写', '不可写') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>./public/upload</td>
                            <td>可写</td>
                            <td>
                                <?= checkResult(is_writable(__DIR__ . '/../../public/upload'), '可写', '不可写') ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="section">
                <div class="title mdui-typo-title">PHP 扩展检查</div>
                <div class="mdui-table-fluid">
                    <table class="mdui-table mdui-table-hoverable">
                        <thead>
                        <tr>
                            <th>必须启用的扩展</th>
                            <th>检查结果</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>gd 或 imagemagick</td>
                            <td>
                                <?= checkResult(
                                    extension_loaded('gd') || extension_loaded('imagemagick'),
                                    '已启用',
                                    '未启用'
                                ) ?>
                                <?= extension_loaded('gd') ? 'gd' : '' ?>
                                <?= extension_loaded('imagemagick') ? 'imagemagick' : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>fileinfo</td>
                            <td>
                                <?= checkResult(extension_loaded('fileinfo'), '已启用', '未启用') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>json</td>
                            <td>
                                <?= checkResult(extension_loaded('json'), '已启用', '未启用') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>pdo</td>
                            <td>
                                <?= checkResult(extension_loaded('pdo'), '已启用', '未启用') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>iconv</td>
                            <td>
                                <?= checkResult(extension_loaded('iconv'), '已启用', '未启用') ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mdui-table-fluid">
                    <table class="mdui-table mdui-table-hoverable">
                        <thead>
                        <tr>
                            <th>可选启用的扩展</th>
                            <th>检查结果</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>curl</td>
                            <td>
                                <?= checkResult(extension_loaded('curl'), '已启用', '未启用', false) ?>
                                <i
                                    class="mdui-icon material-icons notice mdui-text-color-theme-icon"
                                    mdui-tooltip="{content: '如果需要将文件上传到阿里云OSS、或七牛云、或又拍云，则需要启用该扩展'}"
                                >error_outline</i>
                            </td>
                        </tr>
                        <tr>
                            <td>ftp</td>
                            <td>
                                <?= checkResult(extension_loaded('ftp'), '已启用', '未启用', false) ?>
                                <i
                                    class="mdui-icon material-icons notice mdui-text-color-theme-icon"
                                    mdui-tooltip="{content: '如果需要将文件上传 ftp 服务器，则需要启用该扩展'}"
                                >error_outline</i>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="actions">
<?php if ($GLOBALS['environmentSuccess']) : ?>
                <button
                    class="mdui-btn next-step next-step-0"
                    <?= $GLOBALS['environmentSuccess'] ? '' : 'disabled' ?>
                >下一步</button>
<?php else : ?>
                <span class="next-step-disabled">环境检查未通过，无法安装。请修复后再次安装</span>
<?php endif; ?>
            </div>
        </div>
        <form class="content database">
            <div class="section">
                <div class="title mdui-typo-title">设置数据库信息</div>
                <div class="form">
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">服务器地址</label>
                        <input class="mdui-textfield-input" name="db_host" value="localhost"/>
                    </div>
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">数据库名称</label>
                        <input class="mdui-textfield-input" name="db_database" value="mdclub"/>
                    </div>
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">用户名</label>
                        <input class="mdui-textfield-input" name="db_username" value="mdclub"/>
                    </div>
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">密码</label>
                        <input class="mdui-textfield-input" name="db_password" autocomplete="off"/>
                    </div>
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">表前缀</label>
                        <input class="mdui-textfield-input" name="db_prefix" value="mc_"/>
                        <div class="mdui-textfield-helper">如果您希望在同一个数据库安装多个MDClub，请修改前缀。</div>
                    </div>
                </div>
            </div>
            <div class="section">
                <div class="title mdui-typo-title">设置管理员信息</div>
                <div class="form">
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">邮箱</label>
                        <input class="mdui-textfield-input" name="admin_email"/>
                    </div>
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">用户名</label>
                        <input class="mdui-textfield-input" name="admin_username"/>
                    </div>
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">密码</label>
                        <input class="mdui-textfield-input" name="admin_password" autocomplete="off"/>
                    </div>
                </div>
            </div>
            <div class="actions">
                <button class="mdui-btn next-step next-step-1" type="submit">下一步</button>
            </div>
        </form>
        <div class="content complete">
            <div class="mdui-typo-headline">恭喜你完成安装</div>
            <div class="mdui-typo">
                <p>现在，你可以前往 <a href="<?= get_host_url() ?>"><?= get_host_url() ?></a> 访问网站首页，或前往 <a href="<?= get_host_url() ?>/admin"><?= get_host_url() ?>/admin</a> 访问后台管理系统。</p>
                <p>建议你先在 <a href="<?= get_host_url() ?>/admin">后台管理系统</a> 中完成站点基本信息设置。</p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/mdui@1.0.0/dist/js/mdui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mdclub-sdk-js@1.0.4/dist/mdclub-sdk.min.js"></script>
<script>
    var $ = mdui.$;
    $(function() {
        var $steppers = $('.steppers');
        var $stepperItem1 = $steppers.find('.item-1');
        var $stepperItem2 = $steppers.find('.item-2');
        var $stepperItem3 = $steppers.find('.item-3');
        var $environment = $('.environment');
        var $database = $('.database');
        var $complete = $('.complete');

        function setCookie(key, value) {
            const date = new Date();
            date.setTime(date.getTime() + 15 * 24 * 3600 * 1000);
            document.cookie = key + '=' + value + '; expires=' + date.toUTCString() + '; path=/';
        }

        function loadStart() {
            if ($('.mc-loading-overlay').length) {
                return;
            }

            $(
                '<div class="mc-loading-overlay"><div class="mdui-spinner mdui-spinner-colorful"></div></div>',
            )
                .appendTo(document.body)
                .reflow()
                .addClass('mc-loading-overlay-show');

            setTimeout(() => {
                $('.mc-loading-overlay').mutation();
            }, 0);

            $.lockScreen();
        }

        function loadEnd() {
            const $overlay = $('.mc-loading-overlay');

            if (!$overlay.length) {
                return;
            }

            $overlay.removeClass('mc-loading-overlay-show').transitionEnd(() => {
                $overlay.remove();
                $.unlockScreen();
            });
        }

        // 到第二步
        $('.next-step-0').on('click', function () {
            $stepperItem1.removeClass('active').addClass('done');
            $stepperItem2.addClass('active');
            $environment.hide();
            $database.show();
        });

        // 创建数据库
        $database.on('submit', function (e) {
            e.preventDefault();

            loadStart();

            $.ajax({
                method: 'POST',
                url: '<?= get_root_url() ?>/install/import_database',
                data: $(e.target).serializeArray(),
                dataType: 'json',
            }).then((response) => {
                switch (response.code) {
                    // 安装成功，到第三步
                    case 0:
                        mdclubSDK.TokenApi.login({
                            name: $('input[name="admin_email"]').val(),
                            password: $('input[name="admin_password"]').val(),
                        }).finally(() => {
                            loadEnd();
                        }).then((tokenResponse) => {
                            setCookie('token', tokenResponse.data.token);
                            $stepperItem2.removeClass('active').addClass('done');
                            $stepperItem3.addClass('active');
                            $database.hide();
                            $complete.show();
                        }).catch(() => {
                            mdui.alert('自动登陆失败', function () {}, { history: false });
                        });
                        break;
                    // 安装失败
                    case 100007:
                        loadEnd();
                        mdui.alert(
                            response.extra_message,
                            '安装失败',
                            function () {},
                            { history: false },
                        );
                        break;
                    // 创建管理员账号时，字段验证失败
                    case 200001:
                        loadEnd();
                        mdui.alert(
                            Object.keys(response.errors).map(function (key) {
                                return '管理员' + response.errors[key];
                            }).join('<br/>'),
                            '安装失败',
                            function () {},
                            { history: false },
                        );
                        break;
                    default:
                        loadEnd();
                        mdui.alert(
                            '安装失败',
                            function() {},
                            { history: false },
                        );
                        break;
                }
            }).catch(() => {
                loadEnd();
                mdui.alert('网络连接失败', function () {}, { history: false });
            });
        });
    });
</script>
</body>
</html>
