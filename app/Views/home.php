<h2>WELLCOME</h2>
<br>
<p><?="The 'JACAT' template can work both when apache is pointing to the '/' instead of the '/public' folder, mind the first option is highly dangerous."?></p>

<h4>Recommended PHP extensions</h4>
<?php 
echo "GD: ", extension_loaded('gd') ? 'OK' : 'MISSING', '<br>';
echo "XML: ", extension_loaded('xml') ? 'OK' : 'MISSING', '<br>';
echo "zip: ", extension_loaded('zip') ? 'OK' : 'MISSING', '<br>';
?>
