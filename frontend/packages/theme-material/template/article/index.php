<?php include dirname(__FILE__) . "/../functions.php"; ?>

<?php
$userId = get_user_id();
$include = ['user', 'topics', 'is_following'];

$articles_recent = get_articles([
  'include' => $include,
  'per_page' => 20,
  'order' => '-create_time',
]);

$articles_popular = get_articles([
  'include' => $include,
  'per_page' => 20,
  'order' => '-vote_count',
]);

$articles_following = $userId ? get_following_articles($userId, [
  'include' => $include,
  'per_page' => 20,
]) : null;

$meta_title = '文章列表';
?>

<?php include dirname(__FILE__) . "/../public/header.php"; ?>

<?php if (!$userId): ?>
  <div id="page-articles" class="mdui-container">
    <div id="recent">
      <div class="item-list mdui-card mdui-card-shadow">
        <?php foreach ($articles_recent['data'] as $article): ?>
          <a class="mc-list-item" href="<?= get_root_url() ?>/articles/<?= $article['article_id'] ?>">
            <div class="mc-user-popover">
              <div class="avatar user-popover-trigger" style="background-image: url(<?= $article['relationships']['user']['avatar']['middle'] ?>);"></div>
            </div>
            <div class="title mdui-text-color-theme-text"><?= $article['title'] ?></div>
            <div class="content mdui-text-color-theme-secondary">
              <div class="snippet"><?= mb_substr(strip_tags($article['content_rendered']), 0, 80) ?></div>
              <div class="meta">
                <div class="update_time" title="<?= date('Y-m-d H:i:s', $article['create_time']) ?>">发布于 <?= date('Y-m-d H:i:s', $article['create_time']) ?></div>
                <div class="replys"><?= $article['comment_count'] ?> 条评论</div>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="mc-loaded">已加载完所有数据</div>
    </div>
    <button class="mdui-fab mdui-fab-fixed mdui-fab-extended mdui-ripple mdui-color-theme">
      <i class="mdui-icon material-icons">add</i>
      <span>写文章</span>
    </button>
  </div>
<?php endif; ?>

<script>
  window.G_ARTICLES_RECENT = <?= json_encode($articles_recent) ?>;
  window.G_ARTICLES_POPULAR = <?= json_encode($articles_popular) ?>;
  window.G_ARTICLES_FOLLOWING = <?= $userId ? json_encode($articles_following) : 'null' ?>;
</script>

<?php include dirname(__FILE__) . "/../public/footer.php"; ?>
