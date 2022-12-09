<footer class="main-footer <?= $footer_class; ?>">
		<?php if (ENVIRONMENT=='development'): ?>
			<div class="float-right d-none d-sm-inline">
				JACAT Version: <strong><?= $jacat_version; ?></strong>, 
				CI Version: <strong><?= CodeIgniter\CodeIgniter::CI_VERSION ?></strong>
				&emsp;&emsp;
			</div>
		<?php endif; ?>&copy; <strong><?= date('Y'); ?></strong> All rights reserved.
</footer>