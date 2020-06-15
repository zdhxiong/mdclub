<?php $NODE_ENV = 'development' ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <meta name="theme-color" content="#ffffff">
    <title><?= get_options()['site_name'] ?> 后台管理</title>
    <?php if ($NODE_ENV === 'production'): ?>
    <link rel="stylesheet" href="<?= get_static_url() ?>/admin/index.7fb83ab4.css">
    <?php endif; ?>
</head>
<body class="mdui-drawer-body-left mdui-appbar-with-toolbar">
<script>
    window.G_API = '<?= get_root_url() ?>/api';
    window.G_ROOT = '<?= get_root_url() ?>';
    window.G_ADMIN_ROOT = '<?= get_root_url() ?>/admin';
    window.G_USER = <?php

    echo get_user_id() ? json_encode(get_user(null)) : 'null' ?>;
</script>
<?php if ($NODE_ENV === 'production'): ?>
<script src="<?= get_static_url() ?>/admin/index.7fb83ab4.js"></script>
<?php else: ?>
<script src="http://localhost:8081/index.js"></script>
<?php endif; ?>
</body>
</html>
