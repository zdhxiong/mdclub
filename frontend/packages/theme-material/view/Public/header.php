<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="renderer" content="webkit"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="Cache-Control" content="no-siteapp"/>
  <meta name="theme-color" content="#ffffff">
  <title><?php echo isset($title) ? $title.' - '.$setting['site_name'] : $setting['site_name'] ?></title>
  <?php 'production'; echo '<link rel="stylesheet" href="__STATIC__/dist/css/vendor.57f492fe4d88c7538803b152fe84a7ca.css"/>'; ?>
  <?php 'production'; echo '<link rel="stylesheet" href="__STATIC__/dist/css/app.9f89b1c04207c140cc3e10a13df2198d.css"/>'; ?>
</head>
<body class="mdui-drawer-body-left">
<div class="mdui-appbar-with-toolbar">

<?php if (!$user_id) include dirname(__FILE__) . '/appbar.php'; ?>
<?php if (!$user_id) include dirname(__FILE__) . '/drawer.php'; ?>
