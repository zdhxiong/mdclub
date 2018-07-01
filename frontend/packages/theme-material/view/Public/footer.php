<script>
  window.G_API = '__ROOT__/api';
  window.G_ROOT = '__ROOT__';
  window.G_USER = <?php echo $user ? json_encode($user) : 'false' ?>;
</script>
<?php 'production';// echo '<script src="__STATIC__/dist/js/manifest.0593ebeaedff8bc2bd79.js"></script>'; ?>
<?php 'production';// echo '<script src="__STATIC__/dist/js/vendor.fb12b35b65ee3d5c85ae.js"></script>'; ?>
<?php 'production';// echo '<script src="__STATIC__/dist/js/app.b31570bb73559787c948.js"></script>'; ?>

<?php 'development'; echo '<script src="http://localhost:8080/app.js"></script>'; ?>
</div>
