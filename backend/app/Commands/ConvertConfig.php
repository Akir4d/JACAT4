<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ConvertConfig extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'AopDevelop';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'convert:config';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Convert a config to module splitted config use -r option to revert it';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'convert:config [ConfigToConvert] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = ['ConfigToConvert' => 'Config to convert'];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = ['-r' => 'Uncovert Config to module'];

    protected $config_name;

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        helper('inflector');

        $this->config_name = $params[0];

        if (!isset($this->config_name)) {
            CLI::error("Config to convert name must be set!");
            return;
        }

        $uncovert = ($params['-r'] ?? CLI::getOption('r'));
        try {
            if (!$uncovert) {
                $this->convertConfig();
            } else {
                $this->unConvertConfig();
            }
        } catch (\Exception $e) {
            CLI::error($e);
        }

    }

    private function convertConfig()
    {
        $module = ucfirst(strtolower($this->config_name));
        $config = APPPATH . "Config/$module.php";
        $configPath = APPPATH . 'Config/Modules/Defaults';
        $fileDest = "$configPath/$module.php";
        if (!file_exists($config))
            return '' . CLI::write("$config not exists!");
        $conf = file_get_contents($config);
        if (strpos($conf, 'namespace') === false)
            return '' . CLI::write("$config is already converted!");

        if (!is_dir($configPath))
            mkdir($configPath);
        $template = "<?php\n" .
            "/*\n* This module was converted to module by spark config:convert\n* Edit config inside Modules/Generic or to revert it\n".
            "* run aop spark config:convert $module -r \n*/\n".
            "include_once(APPPATH . 'Config/Modules/Shared.php');\n" .
            "include_once(getModuleConfig('$module'));\n";
        if (!file_exists($fileDest)) {
            file_put_contents($fileDest, $conf);
            file_put_contents($config, $template);
            CLI::write('Config converted!');
        } else {
            CLI::error("File Destination exist, remove it first!");
        }

    }

    private function unConvertConfig()
    {
        $module = ucfirst(strtolower($this->config_name));
        $config = APPPATH . "Config/$module.php";
        $configPath = APPPATH . 'Config/Modules/Defaults';
        $fileSrc = "$configPath/$module.php";

        if (!file_exists($fileSrc))
            return '' . CLI::error("$fileSrc not exists!");

        $conf = file_get_contents($fileSrc);

        if (strpos($conf, 'namespace') === false)
            return '' . CLI::error("$config file doesn't contains namespace, aborting!");

        file_put_contents($config, $conf);
        unlink($fileSrc);
        CLI::write('Config unconverted, split module config is disabled for this module!');
    }
}