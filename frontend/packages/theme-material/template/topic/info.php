<?php include dirname(__FILE__) . "/../functions.php"; ?>

<?php
$topic_id = get_route()->getArgument('topic_id');
$topic = get_topic($topic_id, ['include' => ['is_following']]);

$questions = get_questions([
  'topic_id' => $topic_id,
  'include' => ['user', 'topics', 'is_following'],
  'per_page' => 20,
  'order' => '-update_time'
]);

$articles = get_articles([
  'topic_id' => $topic_id,
  'include' => ['user', 'topics', 'is_following'],
  'per_page' => 20,
  'order' => '-update_time'
]);

$meta_title = $topic['name'];
?>

<?php include dirname(__FILE__) . "/../public/header.php"; ?>

<?php if (!get_user_id()): ?>
  <div id="page-topic" class="mdui-container">
    <div class="mc-nav">
      <button class="back mdui-btn mdui-color-theme mdui-ripple">
        <i class="mdui-icon mdui-icon-left material-icons">arrow_back</i>返回
      </button>
    </div>
    <div class="mdui-card mdui-card-shadow topic">
      <div class="info">
        <div class="cover" style="background-image: url(<?= $topic['cover']['small'] ?? '' ?>);"></div>
        <div class="main">
          <div class="name"><?= $topic['name'] ?></div>
          <div class="meta mdui-text-color-theme-secondary">
            <span><?= $topic['question_count'] ?> 个提问</span>
            <span><?= $topic['article_count'] ?> 篇文章</span>
          </div>
          <div class="description mdui-text-color-theme-secondary"><?= $topic['description'] ?></div>
        </div>
      </div>
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
    <div class="contexts mdui-card">
      <div class="mc-tab mdui-tab">
        <a href="#questions" class="mdui-ripple mdui-tab-active">
          提问<span><?= $topic['question_count'] ?></span>
        </a>
        <a href="#articles" class="mdui-ripple">
          文章<span><?= $topic['article_count'] ?></span>
        </a>
        <div class="mdui-tab-indicator" style="left: 0px; width: 160px;"></div>
      </div>
      <div id="questions" style="">
        <div class="mc-list-header">
          <div class="title">共 <?= $topic['question_count'] ?> 个提问</div>
          <button class="mdui-btn mdui-btn-dense">
            更新时间（从晚到早）<i class="mdui-icon mdui-icon-right material-icons mdui-text-color-theme-icon">arrow_drop_down</i>
          </button>
        </div>
        <div class="item-list">
          <?php foreach ($questions['data'] as $question): ?>
            <a class="mc-list-item" href="<?= get_root_url() ?>/questions/<?= $question['question_id'] ?>">
              <div class="mc-user-popover">
                <div class="avatar user-popover-trigger" style="background-image: url(<?= $question['relationships']['user']['avatar']['small'] ?? '' ?>);"></div>
              </div>
              <div class="title mdui-text-color-theme-text"><?= $question['title'] ?></div>
              <div class="content mdui-text-color-theme-secondary">
                <div class="snippet"><?= mb_substr(strip_tags($question['content_rendered']), 0, 80) ?></div>
                <div class="meta">
                  <div class="update_time" title="<?= date('Y-m-d H:i:s', $question['create_time']) ?>">发布于 <?= date('Y-m-d H:i:s', $question['create_time']) ?></div>
                  <div class="replys"><?= $question['answer_count'] ?> 个回答</div>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
        <div class="mc-loaded">已加载完所有数据</div>
      </div>
      <div id="articles" style="display: none;">
        <div class="mc-list-header">
          <div class="title">共 <?= $topic['article_count'] ?> 篇文章</div>
          <button class="mdui-btn mdui-btn-dense">
            更新时间（从晚到早）<i class="mdui-icon mdui-icon-right material-icons mdui-text-color-theme-icon">arrow_drop_down</i>
          </button>
        </div>
        <div class="item-list">
          <?php foreach ($articles['data'] as $article): ?>
            <a class="mc-list-item" href="<?= get_root_url() ?>/articles/<?= $article['article_id'] ?>">
              <div class="mc-user-popover">
                <div class="avatar user-popover-trigger" style="background-image: url(<?= $article['relationships']['user']['avatar']['small'] ?? '' ?>);"></div>
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
    </div>
  </div>
<?php endif; ?>

<script>
  window.G_TOPIC = <?= json_encode($topic) ?>;
  window.G_TOPIC_QUESTIONS = <?= json_encode($questions) ?>;
  window.G_TOPIC_ARTICLES = <?= json_encode($articles) ?>;
</script>

<?php include dirname(__FILE__) . "/../public/footer.php"; ?>
