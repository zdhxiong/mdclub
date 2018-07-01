<?php include dirname(__FILE__) . "/../Public/header.php"; ?>

<div class="mdui-container">

  <?php if (!$user_id): ?>
  <div>
    <div id="recommended">
      <div class="mdui-row-xs-2 mdui-row-sm-3 mdui-row-md-4">
        <?php foreach ($recommended_topics['data'] as $key => $topic): ?>
          <div class="mdui-col">
            <div class="mdui-card item" style="background-image: url('<?php echo $topic['cover']['s'] ?>')">
              <a href="" class="info mdui-ripple">
                <div class="name"><?php echo $topic['name'] ?></div>
                <div class="follower"><?php echo $topic['follower_count'] ?> 人关注</div>
              </a>
              <div class="actions">
                <button class="mdui-btn mdui-btn-dense mdui-ripple mdui-text-color-theme follow">关注</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div>

<script>
  window.G_FOLLOWING_TOPICS = <?php echo isset($following_topics) ? json_encode($following_topics) : 'false' ?>;
  window.G_RECOMMENDED_TOPICS = <?php echo isset($recommended_topics) ? json_encode($recommended_topics) : 'false' ?>;
</script>

<?php include dirname(__FILE__) . "/../Public/footer.php"; ?>
