<?php include dirname(__FILE__) . "/../Public/header.php"; ?>

<?php if (!$user_id): ?>
<div id="page-questions" class="mdui-container">
  <div id="recent">
    <div class="item-list">
      <?php foreach ($recent_questions['data'] as $question): ?>
      <a href="__ROOT__/questions/<?php echo $question['question_id'] ?>" class="item">
        <div class="avatar" style="background-image: url('<?php echo $question['relationship']['user']['avatar']['m'] ?>')"></div>
        <div class="content">
          <div class="title"><?php echo $question['title'] ?></div>
          <div class="meta">
            <div class="username"><?php echo $question['relationship']['user']['username'] ?></div>
          </div>
        </div>
        <div class="more">
          <div class="answer_count"><?php echo $question['answer_count'] ? $question['answer_count'] : '' ?></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
  window.G_RECENT_QUESTIONS = <?php echo isset($recent_questions) ? json_encode($recent_questions) : 'false' ?>
</script>

<?php include dirname(__FILE__) . "/../Public/footer.php"; ?>
