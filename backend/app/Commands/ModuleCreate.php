<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ModuleCreate extends BaseCommand
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
    protected $name = 'module:create';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Create Codeigniter Module in Config and Contoller';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'module:create [ModuleName] [Options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = ['ModuleName' => 'Module name to create'];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];


    protected $module_name;

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        helper('inflector');

        $this->module_name = $params[0];

        if (!isset($this->module_name)) {
            CLI::error("Module name must be set!");
            return;
        }

        $this->module_name = ucfirst($this->module_name);

        try {
            $this->createConfig();
            $this->createController();
            $this->createModel();
            $this->createView();

            CLI::write('Module created!');
        } catch (\Exception $e) {
            CLI::error($e);
        }
    }

    /**
     * Create Config File
     */
    protected function createConfig()
    {
        $configPath = APPPATH . 'Config/' . $this->module_name;

        mkdir($configPath);

        if (!file_exists($configPath . '/Module.php')) {
            $template = "<?php\n\n".
            "namespace Config\Modules\\$this->module_name;\n\n".
            "use CodeIgniter\Config\BaseConfig;\n\n".
            "class Module extends BaseConfig\n\n".
            "\n{ \n \n }\n";
            file_put_contents($configPath . '/Module.php', $template);
        } else {
            CLI::error("Can't Create Module Config! Old File Exists!");
        }
    }

    /**
     * Create Controller File
     */
    protected function createController()
    {
        $controllerPath = APPPATH . 'Controllers/' . $this->module_name;

        mkdir($controllerPath);

        if (!file_exists($controllerPath . '/ModuleController.php')) {
            $template = "<?php\n\n".
            "namespace App\Controllers\\$this->module_name;\n\n".
            "use CodeIgniter\Controller;\n".
            "use CodeIgniter\HTTP\CLIRequest;\n".
            "use CodeIgniter\HTTP\IncomingRequest;\n".
            "use CodeIgniter\HTTP\RequestInterface;\n".
            "use CodeIgniter\HTTP\ResponseInterface;\n".
            "use Psr\Log\LoggerInterface;\n\n".
            "use App\Controllers\JacatController;\n\n".
            "/**\n".
            " * Generic controller that extends\n".
            " */\n".
            "abstract class ModuleController extends JacatController\n".
            "{\n".
            "    public function initController(RequestInterface \$request, ResponseInterface \$response, LoggerInterface \$logger)\n".
            "    {\n".
            "        \$this->helpers = array_merge(\$this->helpers, ['setting']);\n\n".
            "        // Do Not Edit This Line\n".
            "        parent::initController(\$request, \$response, \$logger);\n\n".
            "        // IMPORTANT! The name of this module\n".
            "        \$this->mModule = '$this->module_name';\n".
            "    }\n".
            "}";
            file_put_contents($controllerPath . '/ModuleController.php', $template);
        } else {
            CLI::error("Can't Create Controller! Old File Exists!");
        }
    }

    /**
     * Create Models File
     */
    protected function createModel()
    {
        $modelPath = APPPATH . $this->module_folder . '/' . $this->module_name . '/Models';

        mkdir($modelPath);

        if (!file_exists($modelPath . '/UserEntity.php')) {
            $template = "<?php namespace App\Modules\\$this->module_name\\Models;
class UserEntity
{
    protected \$id;
    protected \$name;
    public function __construct()
    {
    }
    public static function of(\$uid, \$uname)
    {
        \$user = new UserEntity();
        \$user->setId(\$uid);
        \$user->setName(\$uname);
        return \$user;
    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return \$this->id;
    }
    /**
     * @param mixed \$id
     */
    public function setId(\$id): void
    {
        \$this->id = \$id;
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        return \$this->name;
    }
    /**
     * @param mixed \$name
     */
    public function setName(\$name): void
    {
        \$this->name = \$name;
    }
}";

            file_put_contents($modelPath . '/UserEntity.php', $template);
        } else {
            CLI::error("Can't Create UserEntity! Old File Exists!");
        }

        if (!file_exists($modelPath . '/UserModel.php')) {

            $template = "<?php namespace App\Modules\\$this->module_name\\Models;
class UserModel
{
    public function getUsers()
    {
        return [
            UserEntity::of('PL0001', 'Mufid Jamaluddin'),
            UserEntity::of('PL0002', 'Andre Jhonson'),
            UserEntity::of('PL0003', 'Indira Wright'),
        ];
    }
}";
            file_put_contents($modelPath . '/UserModel.php', $template);
        } else {
            CLI::error("Can't Create UserModel! Old File Exists!");
        }
    }

    /**
     * Create View
     */
    protected function createView()
    {
        if ($this->view_folder !== $this->module_folder)
            $view_path = APPPATH . $this->view_folder . '/' . strtolower($this->module_name);
        else
            $view_path = APPPATH . $this->module_folder . '/' . $this->module_name . '/Views';

        mkdir($view_path);

        if (!file_exists($view_path . '/dashboard.php')) {
            $template = '';

            file_put_contents($view_path . '/dashboard.php', $template);
        } else {
            CLI::error("Can't Create View! Old File Exists!");
        }

    }
}