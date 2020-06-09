<?php include dirname(__FILE__) . "/functions.php"; ?>
<?php include dirname(__FILE__) . "/public/header.php"; ?>

<?php
$user_id = get_user_id();

// 推荐话题
$topics_recommended = get_topics([
  'include' => 'is_following',
  'order' => '-follower_count',
  'per_page' => 12,
]);

// 最近更新提问
$questions_recent = get_questions([
  'include' => ['user', 'topics', 'is_following'],
  'per_page' => 5,
  'order' => '-update_time',
]);

// 最近热门提问
$questions_popular = get_questions([
 'include' => ['user', 'topics', 'is_following'],
 'per_page' => 5,
 'order' => '-answer_count',
]);

// 最近发布文章
$articles_recent = get_articles([
  'include' => ['user', 'topics', 'is_following'],
  'per_page' => 5,
  'order' => '-create_time',
]);

// 最近热门文章
$articles_popular = get_articles([
 'include' => ['user', 'topics', 'is_following'],
 'per_page' => 5,
 'order' => '-vote_count',
]);
?>

<?php if (!$user_id): ?>
  <div id="page-index" class="mdui-container">
    <a class="header mdui-ripple" href="<?= get_root_url() ?>/topics#recommended">
      <div class="mdui-text-color-theme-secondary">推荐话题</div>
      <i class="mdui-icon material-icons mdui-text-color-theme">arrow_forward</i>
    </a>
    <div class="topics-wrapper">
      <div class="topics">
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
    </div>
    <div class="items-wrapper">
      <div class="items-container">
        <div class="items mdui-card">
          <a class="header mdui-ripple" href="<?= get_root_url() ?>/questions">
            <div class="mdui-text-color-theme-secondary">最近更新提问</div>
            <i class="mdui-icon material-icons mdui-text-color-theme">arrow_forward</i>
          </a>
          <div class="content">
            <?php foreach ($questions_recent['data'] as $question): ?>
              <a class="item" href="<?= get_root_url() ?>/questions/<?= $question['question_id'] ?>">
                <div class="mc-user-popover">
                  <div class="avatar user-popover-trigger" style="background-image: url(<?= $question['relationships']['user']['avatar']['middle'] ?>);"></div>
                </div>
                <div class="title mdui-text-color-theme-text"><?= $question['title'] ?></div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="items-container">
        <div class="items mdui-card">
          <a class="header mdui-ripple" href="<?= get_root_url() ?>/questions#popular">
            <div class="mdui-text-color-theme-secondary">最近热门提问</div>
            <i class="mdui-icon material-icons mdui-text-color-theme">arrow_forward</i>
          </a>
          <div class="content">
            <?php foreach ($questions_popular['data'] as $question): ?>
              <a class="item" href="<?= get_root_url() ?>/questions/<?= $question['question_id'] ?>">
                <div class="mc-user-popover">
                  <div class="avatar user-popover-trigger" style="background-image: url(<?= $question['relationships']['user']['avatar']['middle'] ?>);"></div>
                </div>
                <div class="title mdui-text-color-theme-text"><?= $question['title'] ?></div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
    <div class="items-wrapper">
      <div class="items-container">
        <div class="items mdui-card">
          <a class="header mdui-ripple" href="<?= get_root_url() ?>/articles">
            <div class="mdui-text-color-theme-secondary">最新文章</div>
            <i class="mdui-icon material-icons mdui-text-color-theme">arrow_forward</i>
          </a>
          <div class="content">
            <?php foreach ($articles_recent['data'] as $article): ?>
              <a class="item" href="<?= get_root_url() ?>/articles/<?= $article['article_id'] ?>">
                <div class="mc-user-popover">
                  <div class="avatar user-popover-trigger" style="background-image: url(<?= $article['relationships']['user']['avatar']['middle'] ?>);"></div>
                </div>
                <div class="title mdui-text-color-theme-text"><?= $article['title'] ?></div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="items-container">
        <div class="items mdui-card">
          <a class="header mdui-ripple" href="<?= get_root_url() ?>/articles#popular">
            <div class="mdui-text-color-theme-secondary">最近热门文章</div>
            <i class="mdui-icon material-icons mdui-text-color-theme">arrow_forward</i>
          </a>
          <div class="content">
            <?php foreach ($articles_popular['data'] as $article): ?>
              <a class="item" href="<?= get_root_url() ?>/articles/<?= $article['article_id'] ?>">
                <div class="mc-user-popover">
                  <div class="avatar user-popover-trigger" style="background-image: url(<?= $article['relationships']['user']['avatar']['middle'] ?>);"></div>
                </div>
                <div class="title mdui-text-color-theme-text"><?= $article['title'] ?></div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<script>
  window.G_INDEX_TOPICS = <?= json_encode($topics_recommended) ?>;
  window.G_INDEX_QUESTIONS_RECENT = <?= json_encode($questions_recent) ?>;
  window.G_INDEX_QUESTIONS_POPULAR = <?= json_encode($questions_popular) ?>;
  window.G_INDEX_ARTICLES_RECENT = <?= json_encode($articles_recent) ?>;
  window.G_INDEX_ARTICLES_POPULAR = <?= json_encode($articles_popular) ?>;
</script>

<?php include dirname(__FILE__) . "/public/footer.php"; ?>
