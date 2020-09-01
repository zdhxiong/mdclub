<?php $NODE_ENV = 'production' ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="renderer" content="webkit"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="Cache-Control" content="no-siteapp"/>
  <meta name="theme-color" content="#ffffff">
  <title><?php
    echo isset($meta_title)
      ? $meta_title . ' - ' . get_options()['site_name']
      : get_options()['site_name'] .
      (
        get_options()['site_description']
          ? ' - ' . get_options()['site_description']
          : ''
      )
    ?></title>
  <?php if ($NODE_ENV === 'production'): ?>
  <link rel="stylesheet" href="<?= get_theme_static_url() ?>/index.a072c8f7.css">
  <?php endif; ?>
</head>
<body class="mdui-drawer-body-left">
<div class="mdui-appbar-with-toolbar mg-app">

<?php if (!get_user_id()) include dirname(__FILE__) . '/appbar.php'; ?>
<?php if (!get_user_id()) include dirname(__FILE__) . '/drawer.php'; ?>
