<?php include dirname(__FILE__) . "/../functions.php"; ?>

<?php
$userId = get_user_id();
$include = ['is_following', 'is_me'];

$users_recommended = get_users([
  'order' => '-follower_count',
  'include' => $include,
  'per_page' => 20,
]);

$users_followees = $userId ? get_followees($userId, [
  'include' => $include,
  'per_page' => 20,
]) : null;

$users_followers = $userId ? get_followers($userId, [
  'include' => $include,
  'per_page' => 20,
]) : null;

$meta_title = '用户列表';
?>

<?php include dirname(__FILE__) . "/../public/header.php"; ?>

<?php if (!$userId): ?>
  <div id="page-users" class="mdui-container">
    <div id="recommended">
      <div class="subheading mdui-text-color-theme-secondary">人员推荐</div>
      <div class="items-wrapper">
        <?php foreach ($users_recommended['data'] as $user): ?>
          <div class="item-inner">
            <div class="item mdui-card">
              <a class="mdui-ripple info" href="<?= get_root_url() ?>/users/<?= $user['user_id'] ?>">
                <div class="avatar" style="background-image: url(<?= $user['avatar']['large'] ?>);"></div>
                <div class="username mdui-text-color-theme-text"><?= $user['username'] ?></div>
                <div class="headline mdui-text-color-theme-secondary"><?= $user['headline'] ?></div>
              </a>
              <div class="actions">
                <button class="mc-icon-button mdui-btn mdui-btn-icon mdui-btn-outlined mc-follow">
                  <i class="mdui-icon material-icons mdui-text-color-theme-icon">star_border</i>
                </button>
                <div class="flex-grow"></div>
                <div class="mc-options-button">
                  <button class="mdui-btn mdui-btn-icon mdui-text-color-theme-icon mdui-ripple">
                    <i class="mdui-icon material-icons">more_vert</i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="mc-loaded">已加载完所有数据</div>
    </div>
  </div>
<?php endif; ?>

<script>
  window.G_USERS_FOLLOWEES = <?= $userId ? json_encode($users_followees) : 'null' ?>;
  window.G_USERS_FOLLOWERS = <?= $userId ? json_encode($users_followers) : 'null' ?>;
  window.G_USERS_RECOMMENDED = <?= json_encode($users_recommended) ?>;
</script>

<?php include dirname(__FILE__) . "/../public/footer.php"; ?>
