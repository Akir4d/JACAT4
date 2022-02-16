<footer class="main-footer <?php echo $footer_class; ?>">
		<?php if (ENVIRONMENT=='development'): ?>
			<div class="float-right d-none d-sm-inline">
				JACAT Version: <strong><?php echo JACAT_VERSION; ?></strong>, 
				CI Version: <strong><?= CodeIgniter\CodeIgniter::CI_VERSION ?></strong>
				&emsp;&emsp;
			</div>
		<?php endif; ?>&copy; <strong><?php echo date('Y'); ?></strong> All rights reserved.
</footer>