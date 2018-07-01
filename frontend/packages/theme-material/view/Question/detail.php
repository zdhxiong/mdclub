<?php include dirname(__FILE__) . "/../Public/header.php"; ?>

<?php if (!$user_id): ?>
<div id="page-question" class="mdui-container">
  <div class="mdui-card mdui-center question">
    <h1 class="mdui-typo-title"><?php echo $question['title'] ?></h1>
    <div class="mc-user-line">
      <a class="avatar" href="__ROOT__/users/<?php echo $question['user_id'] ?>" style="background-image: url('<?php echo $question['relationship']['user']['avatar']['m'] ?>')"></a>
      <div class="info">
        <div class="username">
          <a href="__ROOT__/users/<?php echo $question['user_id'] ?>"><?php echo $question['relationship']['user']['username'] ?></a>
        </div>
        <div class="headline"><?php echo $question['relationship']['user']['headline'] ?></div>
      </div>
    </div>
    <div class="mdui-typo content"><?php echo $question['content_rendered'] ?></div>
  </div>
  <div class="mdui-typo-headline answers-count"><?php echo '共 ' . $answers['pagination']['total'] . ' 个回答' ?></div>
  <div class="mdui-card mdui-center answers">
    <?php foreach ($answers['data'] as $answer): ?>
    <div class="item">
      <div class="mc-user-line">
        <a class="avatar" href="__ROOT__/users/<?php echo $answer['user_id'] ?>" style="background-image: url('<?php echo $answer['relationship']['user']['avatar']['m'] ?>')"></a>
        <div class="info">
          <div class="username">
            <a href="__ROOT__/users/<?php echo $answer['user_id'] ?>"><?php echo $answer['relationship']['user']['username'] ?></a>
          </div>
          <div class="headline"><?php echo $answer['relationship']['user']['headline'] ?></div>
        </div>
      </div>
      <div class="content mdui-typo"><?php echo $answer['content_rendered'] ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<script>
  window.G_QUESTION = <?php echo isset($question) ? json_encode($question) : 'false' ?>;
  window.G_ANSWERS = <?php echo isset($answers) ? json_encode($answers) : 'false' ?>;
</script>

<?php include dirname(__FILE__) . "/../Public/footer.php"; ?>
