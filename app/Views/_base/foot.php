
	<?php
		foreach ($scripts['foot'] as $file)
		{
			$url = starts_with($file, 'http') ? $file : base_url($file);
			echo "<script src='$url'></script>".PHP_EOL;
		}
	?>

	<?php // Google Analytics ?>
	<?php   echo view('_partials/ga') ?>
</body>
</html>