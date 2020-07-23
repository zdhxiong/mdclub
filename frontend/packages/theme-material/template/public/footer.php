<script>
  window.G_API = '<?= get_root_url() ?>/api';
  window.G_ROOT = '<?= get_root_url() ?>';
  window.G_USER = <?php

  echo get_user_id() ? json_encode(get_user(null, ['include' => ['is_following']])) : 'null' ?>;
  window.G_OPTIONS = <?= json_encode(get_options()) ?>;
</script>
<?php if ($NODE_ENV === 'production'): ?>
<script src="<?= get_theme_static_url() ?>/index.7a2e891c.js"></script>
<?php else: ?>
<script src="http://localhost:8080/index.js"></script>
<?php endif; ?>
</div>
