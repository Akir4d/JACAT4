<div class="wrapper">
	<?php echo view('_partials/navbar'); ?>
	<div class="content-wrapper">
		<section class="content">
			<div class="container">
				<div class="content-header">
					<h1>
						<?php echo $page_title; ?>
					</h1>
				</div>
				<div class="row">
					<?php echo $inject_after_page_title; ?>
					<?php echo view('_partials/breadcrumb'); ?>
					<?php echo view($inner_view); ?>
				</div>
			</div>
		</section>
	</div>
	<?php echo $inject_before_footer; ?>
	<?php echo view('_partials/footer'); ?>
</div>