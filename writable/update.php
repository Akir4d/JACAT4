<?php

if(!extension_loaded('intl')){
  echo "<h1>You have to enable php-intl in your php.ini configuration</h1>";
  die();
}
$composerPath = realpath(FCPATH . '..');
$updateDir = realpath($composerPath . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'versions');
$updateFile = $updateDir . DIRECTORY_SEPARATOR . 'jacat_update_' . MYAPP_VERSION . '.info.txt';


if (!file_exists($composerPath . DIRECTORY_SEPARATOR . 'vendor') || !file_exists($updateFile)) {
    function removeFolder($folderName)
    {
        if (is_dir($folderName)) $folderHandle = opendir($folderName);
        if (!$folderHandle) return false;
        while ($file = readdir($folderHandle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($folderName . "/" . $file))
                    unlink($folderName . "/" . $file);
                else
                    removeFolder($folderName . '/' . $file);
            }
        }
        closedir($folderHandle);
        rmdir($folderName);
        return true;
    }
    $updateTemp = $updateDir . DIRECTORY_SEPARATOR . 'composer';
    if (!file_exists($updateTemp)) mkdir($updateTemp);
    if (!file_exists($composerPath . '/.env')) file_put_contents($composerPath . '/.env', file_get_contents($composerPath . '/env'));
    if (ob_get_level() == 0) ob_start();
    $cv = 'https://getcomposer.org/download/latest-stable/composer.phar';
    echo "<h1>JACAT AUTO UPDATE STARTED: please wait!</h1><p>Downloading composer from $cv</p>";
    ob_flush();
    flush();
    chdir($composerPath);
    $exec = $composerPath . DIRECTORY_SEPARATOR .  'composer.phar';
    file_put_contents($updateFile, 'update', FILE_APPEND);
    file_put_contents($exec, file_get_contents($cv));
    if (file_exists($updateDir . '/composer/vendor/autoload.php') == false) {
        $composerPhar = new Phar($exec);
        //php.ini setting phar.readonly must be set to 0
        $composerPhar->extractTo($updateTemp);
    }
    require_once($updateDir . '/composer/vendor/autoload.php');
    require_once  $composerPath . '/composer.php';
    $composer = new ComposerCommandLine();
    $res = $composer->update();
    echo '<pre>' . $res . '</pre>';
    file_put_contents($updateFile, $res, FILE_APPEND);
    unlink($exec);
    removeFolder($updateTemp);
    echo '<script>setTimeout(()=>parent.window.location.reload(true), 10000);</script>';
    ob_flush();
    flush();
    die();
}
