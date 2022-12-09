<h2>WELLCOME</h2>
<br>
<p><?="The 'JACAT' template"?></p>
<a href="<?=base_url('emergency')?>" class="btn btn-primary">Check the emergency console first</a>
<h4>Recommended PHP extensions</h4>
<?php 
echo "GD: ", extension_loaded('gd') ? 'OK' : 'MISSING', '<br>';
echo "XML: ", extension_loaded('xml') ? 'OK' : 'MISSING', '<br>';
echo "zip: ", extension_loaded('zip') ? 'OK' : 'MISSING', '<br>';
?>
