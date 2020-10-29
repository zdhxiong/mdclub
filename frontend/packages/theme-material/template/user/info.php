<?php include dirname(__FILE__) . "/../functions.php"; ?>

<?php
// 当前访问的用户信息
$user_id = get_route()->getArgument('user_id');

$user = get_user($user_id, [
  'include' => 'is_following',
]);

$questions = get_questions([
  'user_id' => $user_id,
  'order' => '-update_time',
  'include' => ['user', 'topics', 'is_following'],
  'per_page' => 20,
]);

$articles = get_articles([
  'user_id' => $user_id,
  'order' => '-create_time',
  'include' => ['user', 'topics', 'is_following'],
  'per_page' => 20,
]);

$answers = get_answers([
  'user_id' => $user_id,
  'order' => '-create_time',
  'include' => ['user','question', 'voting'],
  'per_page' => 20,
]);

$meta_title = $user['username'];
?>

<?php include dirname(__FILE__) . "/../public/header.php"; ?>

<?php if (!get_user_id()): ?>
  <div id="page-user" class="mdui-container">
    <div class="user mdui-card mdui-card-shadow">
      <div class="cover" style="background-image: url(<?= $user['cover']['large'] ?? '' ?>); background-position-y: 0px;">
        <div class="mc-cover-upload">
          <button class="upload-btn mdui-btn mdui-btn-icon mdui-ripple" type="button" title="点击上传封面">
            <i class="mdui-icon material-icons">photo_camera</i>
          </button>
          <input type="file" title=" " accept="image/jpeg,image/png">
        </div>
      </div>
      <div class="info">
        <div class="avatar-box">
          <div class="mc-avatar-upload">
            <button class="upload-btn mdui-btn mdui-btn-icon mdui-ripple" type="button" title="点击上传头像">
              <i class="mdui-icon material-icons">photo_camera</i>
            </button>
            <input type="file" title=" " accept="image/jpeg,image/png">
          </div>
          <img src="<?= $user['avatar']['large'] ?? '' ?>" class="avatar">
        </div>
        <div class="profile">
          <div class="meta username mdui-text-color-theme-text"><?= $user['username'] ?></div>
          <div class="meta">
            <i class="mdui-icon material-icons mdui-text-color-theme-icon">credit_card</i>
            <div class=""><?= $user['headline'] ?></div>
          </div>
          <div class="meta">
            <i class="mdui-icon material-icons mdui-text-color-theme-icon">insert_link</i>
            <div class=""><?= $user['blog'] ?></div>
          </div>
          <div class="meta">
            <i class="mdui-icon material-icons mdui-text-color-theme-icon">location_city</i>
            <div class=""><?= $user['company'] ?></div>
          </div>
          <div class="meta">
            <i class="mdui-icon material-icons mdui-text-color-theme-icon">location_on</i>
            <div class=""><?= $user['location'] ?></div>
          </div>
          <div class="meta">
            <i class="mdui-icon material-icons mdui-text-color-theme-icon">description</i>
            <div class="mdui-typo">
              <?= $user['bio'] ?>
            </div>
          </div>
          <button class="fold-button mdui-btn">
            <i class="mdui-icon-left mdui-icon material-icons mdui-text-color-theme-icon">keyboard_arrow_up</i>收起详细资料
          </button>
        </div>
      </div>
      <div class="actions">
        <button class="edit mdui-btn mdui-btn-icon mdui-btn-outlined">
          <i class="mdui-icon material-icons mdui-text-color-theme-icon">edit</i>
        </button>
        <div class="follow">
          <button class="followers mdui-btn mdui-text-color-theme-secondary"><?= $user['follower_count'] ?> 人关注</button>
          <div class="divider"></div>
          <button class="followees mdui-btn mdui-text-color-theme-secondary">关注了 <?= $user['followee_count'] ?> 人</button>
        </div>
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
        <a href="#questions" class="mdui-ripple mdui-tab-active">提问<span><?= $user['question_count'] ?></span></a>
        <a href="#answers" class="mdui-ripple">回答<span><?= $user['answer_count'] ?></span></a>
        <a href="#articles" class="mdui-ripple">文章<span><?= $user['article_count'] ?></span></a>
        <div class="mdui-tab-indicator" style="left: 0px; width: 160px;"></div>
      </div>
      <div id="questions">
        <div class="mc-list-header">
          <div class="title">共 <?= $user['question_count'] ?> 个提问</div>
          <button class="mdui-btn mdui-btn-dense">
            更新时间（从晚到早）<i class="mdui-icon mdui-icon-right material-icons mdui-text-color-theme-icon">arrow_drop_down</i>
          </button>
        </div>
        <div class="item-list">
          <?php foreach ($questions['data'] as $question): ?>
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
    </div>
  </div>
<?php endif; ?>

<script>
  window.G_INTERVIEWEE = <?= json_encode($user) ?>;
  window.G_USER_QUESTIONS = <?= json_encode($questions) ?>;
  window.G_USER_ARTICLES = <?= json_encode($articles) ?>;
  window.G_USER_ANSWERS = <?= json_encode($answers) ?>;
</script>

<?php include dirname(__FILE__) . "/../public/footer.php"; ?>
