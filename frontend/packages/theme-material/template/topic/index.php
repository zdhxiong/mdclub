<?php include dirname(__FILE__) . "/../functions.php"; ?>

<?php
$user_id = get_user_id();

// 推荐话题
$topics_recommended = get_topics([
  'include' => 'is_following',
  'order' => '-follower_count',
  'per_page' => 20,
]);

// 已关注话题
$topics_following = $user_id ? get_following_topics($user_id, [
  'include' => 'is_following',
  'per_page' => 20,
]) : null;

$meta_title = '话题列表';
?>

<?php include dirname(__FILE__) . "/../public/header.php"; ?>

<?php if (!$user_id): ?>
  <div id="page-topics" class="mdui-container">
    <div id="recommended">
      <div class="items-wrapper">
        <?php foreach ($topics_recommended['data'] as $topic): ?>
        <div class="mc-topic-item item-inner">
          <div class="item mdui-card" style="background-image: url(<?= $topic['cover']['small'] ?>);">
            <a class="mdui-ripple info" href="<?= get_root_url() ?>/topics/<?= $topic['topic_id'] ?>">
              <div class="name mdui-text-color-theme-text"><?= $topic['name'] ?></div>
            </a>
            <div class="actions">
              <button class="mc-icon-button mdui-btn mdui-btn-icon mdui-btn-outlined mc-follow">
                <i class="mdui-icon material-icons mdui-text-color-theme-icon">star_border</i>
              </button>
              <button class="followers mdui-btn mdui-ripple mdui-text-color-theme-secondary"><?= $topic['follower_count'] ?> 人关注</button>
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
  window.G_TOPICS_FOLLOWING = <?= $user_id ? json_encode($topics_following) : 'null' ?>;
  window.G_TOPICS_RECOMMENDED = <?= json_encode($topics_recommended) ?>;
</script>

<?php include dirname(__FILE__) . "/../public/footer.php"; ?>
