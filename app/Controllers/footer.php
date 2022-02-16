<footer class="main-footer <?php echo $footer_class; ?>">
    <?php if (ENVIRONMENT == 'development') : ?>
        <div class="float-right d-none d-sm-inline">
                JACAT Version: <strong><?php echo JACAT_VERSION; ?></strong>,
                CI Version: <strong><?php echo CI_VERSION; ?></strong>,
                Elapsed Time: <strong>{elapsed_time}</strong> seconds,
                : <strong><?= memory_get_usage() ?></strong>
        </div>
        <?php endif; ?>&copy; <strong><?php echo date('Y'); ?></strong> All rights reserved.
</footer>