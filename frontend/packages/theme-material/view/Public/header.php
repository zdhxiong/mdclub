<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="renderer" content="webkit"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="Cache-Control" content="no-siteapp"/>
  <meta name="theme-color" content="#ffffff">
  <title><?php echo isset($title) ? $title.' - '.$options['site_name'] : $options['site_name'] ?></title>
  <?php 'production'; echo '<link rel="stylesheet" href="__STATIC__/dist/css/vendor.173bc343aa556e66342d7444ad2c42e9.css"/>'; ?>
  <?php 'production'; echo '<link rel="stylesheet" href="__STATIC__/dist/css/app.ca5d229ab5dff22b7bc6e148f629ba09.css"/>'; ?>
</head>
<body class="mdui-drawer-body-left">
<div class="mdui-appbar-with-toolbar">

<?php if (!$user_id) include dirname(__FILE__) . '/appbar.php'; ?>
<?php if (!$user_id) include dirname(__FILE__) . '/drawer.php'; ?>
