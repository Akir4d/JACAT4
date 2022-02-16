<div class="wrapper">
    <nav class="main-header <?php echo $navbar_class; ?>" role="navigation">
            <?php  echo view('_partials/navbar'); ?>
    </nav>
    <div class="content-wrapper">
        <section class="content">
            <?php  echo view($inner_view); ?>
        </section>
    </div>
    <?php  echo view('_partials/footer'); ?>
</div>