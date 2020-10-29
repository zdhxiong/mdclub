<?php include dirname(__FILE__) . "/../functions.php"; ?>

<?php
$userId = get_user_id();
$include = ['user', 'topics', 'is_following'];

$questions_recent = get_questions([
  'include' => $include,
  'per_page' => 20,
  'order' => '-update_time',
]);

$questions_popular = get_questions([
  'include' => $include,
  'per_page' => 20,
  'order' => '-vote_count',
]);

$questions_following = $userId ? get_following_questions($userId, [
  'include' => $include,
  'per_page' => 20,
]) : null;

$meta_title = '提问列表';
?>

<?php include dirname(__FILE__) . "/../public/header.php"; ?>

<?php if (!$userId): ?>
  <div id="page-questions" class="mdui-container">
    <div id="recent">
      <div class="item-list mdui-card mdui-card-shadow">
        <?php foreach ($questions_recent['data'] as $question): ?>
          <a class="mc-list-item" href="<?= get_root_url() ?>/questions/<?= $question['question_id'] ?>">
            <div class="mc-user-popover">
              <div class="avatar user-popover-trigger" style="background-image: url(<?= $question['relationships']['user']['avatar']['middle'] ?? '' ?>);"></div>
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
      <div class="mc-loaded mdui-hidden">已加载完所有数据</div>
    </div>
    <button class="mdui-fab mdui-fab-fixed mdui-fab-extended mdui-ripple mdui-color-theme">
      <i class="mdui-icon material-icons">add</i>
      <span>提问题</span>
    </button>
  </div>
<?php endif; ?>

<script>
  window.G_QUESTIONS_RECENT = <?= json_encode($questions_recent) ?>;
  window.G_QUESTIONS_POPULAR = <?= json_encode($questions_popular) ?>;
  window.G_QUESTIONS_FOLLOWING = <?= $userId ? json_encode($questions_following) : 'null' ?>;
</script>

<?php include dirname(__FILE__) . "/../public/footer.php"; ?>
