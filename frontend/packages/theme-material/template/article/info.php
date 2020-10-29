<?php include dirname(__FILE__) . "/../functions.php"; ?>

<?php
$article_id = get_route()->getArgument('article_id');

$article = get_article($article_id, [
  'include' => ['user', 'topics', 'is_following']
]);

$comments = get_comments([
  'commentable_type' => 'article',
  'commentable_id' => $article_id,
  'include' => ['user', 'voting'],
  'order' => 'create_time',
]);
$comments['data'] = comments_transformer($comments['data']);

$meta_title = $article['title'];
?>

<?php include dirname(__FILE__) . "/../public/header.php"; ?>

<?php if (!get_user_id()) : ?>
  <div id="page-article" class="mdui-container">
    <div class="mc-nav">
      <button class="back mdui-btn mdui-color-theme mdui-ripple">
        <i class="mdui-icon mdui-icon-left material-icons">arrow_back</i>返回
      </button>
    </div>
    <div class="mdui-card mdui-card-shadow article">
      <h1 class="title"><?= $article['title'] ?></h1>
      <div class="mc-user-line">
        <div class="mc-user-popover">
          <a class="avatar user-popover-trigger" href="<?= get_root_url() ?>/users/<?= $article['user_id'] ?>" style="background-image: url(<?= $article['relationships']['user']['avatar']['middle'] ?? '' ?>);"></a>
          <a class="username user-popover-trigger mdui-text-color-theme-text" href="<?= get_root_url() ?>/users/<?= $article['user_id'] ?>"><?= $article['relationships']['user']['username'] ?></a>
          <div class="headline mdui-text-color-theme-secondary"><?= $article['relationships']['user']['headline'] ?></div>
          <div class="more">
            <span class="time mdui-text-color-theme-secondary" title="<?= date('Y-m-d H:i:s', $article['create_time']) ?>"><?= date('Y-m-d H:i:s', $article['create_time']) ?></span>
          </div>
        </div>
      </div>
      <div class="mdui-typo content">
        <?= $article['content_rendered'] ?>
      </div>
      <div class="mc-topics-bar">
        <?php foreach ($article['relationships']['topics'] as $topic): ?>
          <a class="mdui-chip mdui-ripple" href="<?= get_root_url() ?>/topics/<?= $topic['topic_id'] ?>">
            <img class="mdui-chip-icon" src="<?= $topic['cover']['small'] ?? '' ?>">
            <span class="mdui-chip-title"><?= $topic['name'] ?></span>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="actions">
        <div class="mc-vote">
          <button class="mc-icon-button mdui-btn mdui-btn-icon mdui-btn-outlined">
            <i class="mdui-icon material-icons mdui-text-color-theme-icon">thumb_up</i>
          </button>
          <button class="mc-icon-button mdui-btn mdui-btn-icon mdui-btn-outlined">
            <i class="mdui-icon material-icons mdui-text-color-theme-icon">thumb_down</i>
          </button>
        </div>
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
<?php endif; ?>

<script>
  window.G_ARTICLE = <?= json_encode($article) ?>;
  window.G_COMMENTS = <?= json_encode($comments) ?>;
</script>

<?php include dirname(__FILE__) . "/../public/footer.php"; ?>
