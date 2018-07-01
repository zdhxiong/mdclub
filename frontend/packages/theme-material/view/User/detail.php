<?php include dirname(__FILE__) . "/../Public/header.php"; ?>

<?php if (!$user_id): ?>
<div id="page-user" class="mdui-container">
  <div class="mdui-card cover" style="background-image: url('<?php echo $_user['cover']['l']; ?>')">
    <div class="gradient mdui-card-media-covered mdui-card-media-covered-gradient"></div>
    <div class="info">
      <div class="avatar-box">
        <img src="<?php echo $_user['avatar']['l']; ?>" class="avatar"/>
      </div>
      <div class="username"><?php echo $_user['username'] ?></div>
      <div class="meta">
        <a href="javascript:void(0)" class="headline"><?php echo $_user['headline'] ?></a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
  window.G__USER = <?php echo json_encode($_user); ?>
</script>

<?php include dirname(__FILE__) . "/../Public/footer.php"; ?>
