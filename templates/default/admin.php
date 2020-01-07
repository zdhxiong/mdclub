<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <title></title>
    <?= $css ?>
    $cssString
</head>
<body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-blue mdui-theme-accent-blue">
<script>
    window.G_API = "<?= $root ?>/api"; // api 地址
    window.G_ROOT = "<?= $root ?>"; // 网站根目录相对路径
    window.G_ADMIN_ROOT = "<?= $root ?>/admin"; // 网站后台根目录相对路径
    window.G_SITE = "<?= $host ?>"; // 网址（含域名）
    window.G_USER = <?= json_encode($user) ?>; // 用户信息
</script>
<?= $js ?>>
</body>
</html>
