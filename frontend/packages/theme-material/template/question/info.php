<?php include dirname(__FILE__) . "/../functions.php"; ?>

<?php
$question_id = get_route()->getArgument('question_id');

// 提问详情页和回答详情页公用
// 若访问的是回答详情页，包含 answer_id；否则不包含
$answer_id = get_route()->getArgument('answer_id');

$question = get_question($question_id, [
  'include' => ['user', 'topics', 'is_following'],
]);

if ($answer_id) {
  // 在回答详情页，仅显示当前回答
  $answer = get_answer($answer_id, [
    'include' => ['user', 'voting'],
  ]);
} else {
  // 在提问详情页，显示回答列表
  $answers = get_answers([
    'question_id' => $question_id,
    'include' => ['user', 'voting'],
    'order' => '-vote_count',
    'per_page' => 20,
  ]);
}

$meta_title = $question['title'];
?>

<?php include dirname(__FILE__) . "/../public/header.php"; ?>

<?php if (!get_user_id()) : ?>
  <div id="page-question" class="mdui-container">
    <div class="mc-nav">
      <button class="back mdui-btn mdui-color-theme mdui-ripple">
        <i class="mdui-icon mdui-icon-left material-icons">arrow_back</i>返回
      </button>
    </div>
    <div class="mdui-card mdui-card-shadow question">
      <h1 class="title"><?= $question['title'] ?></h1>
      <div class="mc-user-line">
        <div class="mc-user-popover">
          <a class="avatar user-popover-trigger" href="<?= get_root_url() ?>/users/<?= $question['user_id'] ?>" style="background-image: url(<?= $question['relationships']['user']['avatar']['middle'] ?>);"></a>
          <a class="username user-popover-trigger mdui-text-color-theme-text" href="<?= get_root_url() ?>/users/<?= $question['user_id'] ?>"><?= $question['relationships']['user']['username'] ?></a>
          <div class="headline mdui-text-color-theme-secondary"><?= $question['relationships']['user']['headline'] ?></div>
          <div class="more">
            <span class="time mdui-text-color-theme-secondary" title="<?= date('Y-m-d H:i:s', $question['create_time']) ?>"><?= date('Y-m-d H:i:s', $question['create_time']) ?></span>
          </div>
        </div>
      </div>
      <div class="mdui-typo content">
        <?= $question['content_rendered'] ?>
      </div>
      <div class="mc-topics-bar">
        <?php foreach ($question['relationships']['topics'] as $topic): ?>
        <a class="mdui-chip mdui-ripple" href="<?= get_root_url() ?>/topics/<?= $topic['topic_id'] ?>">
          <img class="mdui-chip-icon" src="<?= $topic['cover']['small'] ?>">
          <span class="mdui-chip-title"><?= $topic['name'] ?></span>
        </a>
        <?php endforeach; ?>
      </div>
      <div class="actions">
        <button class="mc-icon-button mdui-btn mdui-btn-icon mdui-btn-outlined mc-follow">
          <i class="mdui-icon material-icons mdui-text-color-theme-icon">star_border</i>
        </button>
        <button class="mc-icon-button mdui-btn mdui-btn-icon mdui-btn-outlined comment">
          <span class="badge">1</span>
          <i class="mdui-icon material-icons mdui-text-color-theme-icon">comment</i>
        </button>
        <div class="flex-grow"></div>
        <div class="mc-options-button">
          <button class="mdui-btn mdui-btn-icon mdui-text-color-theme-icon mdui-ripple">
            <i class="mdui-icon material-icons">more_vert</i>
          </button>
        </div>
      </div>
    </div>

    <?php if ($answer_id): // 在回答详情页，显示回答 ?>
      <a class="all-answers mdui-text-color-theme-text" href="<?= get_root_url() ?>/questions/<?= $question['question_id'] ?>">查看全部 <?= $question['answer_count'] ?> 个回答</a>
      <div class="mdui-card answers">
        <div class="item">
          <div class="mc-user-line">
            <div class="mc-user-popover">
              <a class="avatar user-popover-trigger" href="<?= get_root_url() ?>/users/<?= $answer['user_id'] ?>" style="background-image: url(<?= $answer['relationships']['user']['avatar']['middle'] ?>);"></a>
              <a class="username user-popover-trigger mdui-text-color-theme-text" href="<?= get_root_url() ?>/users/<?= $answer['user_id'] ?>"><?= $answer['relationships']['user']['username'] ?></a>
              <div class="headline mdui-text-color-theme-secondary"><?= $answer['relationships']['user']['headline'] ?></div>
              <div class="more">
                <span class="time mdui-text-color-theme-secondary" title="<?= date('Y-m-d H:i:s', $answer['create_time']) ?>"><?= date('Y-m-d H:i:s', $answer['create_time']) ?></span>
              </div>
            </div>
          </div>
          <div class="content mdui-typo">
            <?= $answer['content_rendered'] ?>
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
            <button class="mc-icon-button mdui-btn mdui-btn-icon mdui-btn-outlined comment">
              <i class="mdui-icon material-icons mdui-text-color-theme-icon">comment</i>
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
      <a class="all-answers mdui-text-color-theme-text" href="<?= get_root_url() ?>/questions/<?= $question['question_id'] ?>">查看全部 <?= $question['answer_count'] ?> 个回答</a>
    <?php endif; ?>
  </div>
<?php endif; ?>

<script>
  window.G_QUESTION = <?= json_encode($question) ?>;

  <?php if ($answer_id): ?>
  window.G_ANSWER = <?= json_encode($answer) ?>;
  <?php else: ?>
  window.G_QUESTION_ANSWERS = <?= json_encode($answers) ?>;
  <?php endif; ?>
</script>

<?php include dirname(__FILE__) . "/../public/footer.php"; ?>
