<?php include dirname(__FILE__) . "/../Public/header.php"; ?>

<script>
  window.G_FOLLOWING_USERS = <?php echo isset($following_users) ? json_encode($following_users) : 'false' ?>;
  window.G_FOLLOWERS_USERS = <?php echo isset($followers_users) ? json_encode($followers_users) : 'false' ?>;
  window.G_RECOMMENDED_USERS = <?php echo isset($recommended_users) ? json_encode($recommended_users) : 'false' ?>;
</script>

<?php include dirname(__FILE__) . "/../Public/footer.php"; ?>
