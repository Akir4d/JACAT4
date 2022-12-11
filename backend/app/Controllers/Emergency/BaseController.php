<?php

namespace App\Controllers\Emergency;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Firebase\JWT\Key;
use Psr\Log\LoggerInterface;
use \Firebase\JWT\JWT;



/**
 * Class BaseController
 * This is a copy of AopBaseController + BaseController + Filters because this module 
 * must be critical mission
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    protected $styles = [];
    protected $headFooter = ['head' => ['row' => [], 'auto' => []], 'footer' => ['row' => [], 'auto' => []]];

    protected $jwtInformation = null;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = \Config\Services::session();
        $this->allowCors([], ENVIRONMENT == 'development');
    }

     /**
      * A White-list for given origins 
      *
      * @param mixed $allowed_domains list of origins you want to white-list
      * @param mixed $forceAll allow from ALL, DO NOT USE IN PRODUCTION!
      * @return void
      */

    protected function allowCors($allowed_domains=[], $forceAll=false)
    {
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $origin = $_SERVER['HTTP_ORIGIN'];
        } else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
            $origin = $_SERVER['HTTP_REFERER'];
        } else {
            $origin = $_SERVER['REMOTE_ADDR'];
        }

        if ($forceAll || in_array($origin, $allowed_domains)) {
            header('Access-Control-Allow-Origin: ' . $origin);
        }

        header("Access-Control-Allow-Headers: Origin, X-API-KEY, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Headers, Authorization, observe, enctype, Content-Length, X-Csrf-Token");
        header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, PATCH, OPTIONS");
        header("Access-Control-Allow-Credentials: true");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            header("HTTP/1.1 200 OK");
            die();
        }

    }

    /**
     * Add Styles to head
     *
     * @param [type] $value type
     * @param string $type
     * @return void
     */
    protected function addStyles($value)
    {
        $uniq = md5($value);
        $this->styles[$uniq] = $value;
    }

    /**
     * Adds Header and Footer stuff!
     *
     * @param string $value
     * @param string $type
     * @param string $position
     * @return void
     */
    protected function addHeadFooter(string $value, string $type = "auto", string $position = "footer")
    {
        $uniq = md5($value);
        switch ($type) {
            case 'row':
                $this->headFooter[$position]['row'][$uniq] = $value;
                break;
            case 'auto':
                $this->headFooter[$position]['auto'][$uniq] = $value;
                break;
        }
    }

    /**
     * Check db is an optional feature that check and populate db
     *
     * @return object
     */
    protected function checkDb(): object
    {
        $migrate = null;
        $status = (object) ['message' => '', 'error' => false, 'debug' => ''];
        try {
            $migrate = \Config\Services::migrations();
        } catch (\Throwable $e) {
            $status->message = "Db Connection Error";
            $status->error = true;
            $status->debug = $e;
        }
        if ($migrate !== null) {
            try {
                $migrate->latest();
            } catch (\Throwable $e) {
                $status->message = "Db Update Error";
                $status->error = true;
                $status->debug = $e;
            }
        }
        return $status;
    }

    protected function checkCredentials(){
        $status = (object) ['message' => '', 'error' => false, 'debug' => ''];
        $log = new \Config\Emergency\Emergency();
        if(strpos($log->login['password'], 'hash goes here')!==false){
            $status->message = "Login is disabled, check the .env file";
            $status->error = true;
            return $status;
        } 

        if (password_verify('admin', $log->login['password'])) {
            $status->message = "Emergency console password is set to default, change it!";
            $status->error = true;
        }
        return $status;
    }

    protected function renderJson(
        array $responseBody,
        int $code = ResponseInterface::HTTP_OK
    ) {
        return $this
            ->response
            ->setStatusCode($code)
            ->setJSON($responseBody);
    }

    /**
     * Just a common function aopRender and aopModularize
     *
     * @param string $module
     * @param string $returnPath
     * @param array $arguments
     * @return object
     */
    protected function aopPreRender(string $module, string $returnPath, array $arguments = []): object
    {
        $config = new \Config\Emergency\Emergency();
        $du = $config->develCi;
        $file = FCPATH . "amodules/$module/index.html";
        $moduleData = "";
        $args = "";
        if (!empty($arguments)) {
            foreach ($arguments as $k => $v) {
                $v = (!(is_string($v) || is_numeric($v))) ? htmlspecialchars(json_encode($v)) : $v;

                $args .= is_numeric($v) ? ' data-' . $k . '=' . $v : ' data-' . $k . '="' . $v . '"';
            }
        }
        if (substr(base_url(), 0, strlen($du)) == $du) {
            $du = $config->develAn;
            $file = $du . '/index.html';
            $moduleData = preg_replace('/<base.*?>/m', '<base href="' . $du . '"' . $args . '/>', file_get_contents($file));
        } else {
            $du = base_url($returnPath) . '/';
            $moduleData = preg_replace('/<base.*?>/m', '<base href="' . $du . '"' . $args . '/>', file_get_contents($file));
        }
        $seg = str_replace(base_url($returnPath), '', base_url($this->request->getPath()));
        $file = 'amodules/' . $module . $seg;
        //echo $file;
        //die();
        if (is_file(FCPATH . $file)) {
            return (object) ['type' => 'redirect', 'file' => $file];
        } else {
            return (object) ['type' => 'module', 'data' => $moduleData];
        }
    }

    /**
     * aopRender renders an angular module in its entirety, with no additional CSS or JS injections.
     *
     * @param string $module Name of module
     * @param string $returnPath Controller path or routing, for example: api/users
     * @param array  $arguments Array of arguments to pass to Angular.
     *               All arguments can be accessed from within the Angular app using
     *               document.getElementsByTagName("base")[0].dataset["key"] or document.getElementsByTagName("base")[0].dataset?.key
     *
     * @return string | \CodeIgniter\HTTP\RedirectResponse
     */
    protected function aopRender(string $module, string $returnPath, array $arguments = []): string | \CodeIgniter\HTTP\RedirectResponse
    {
        $pre = $this->aopPreRender($module, $returnPath, $arguments);
        if ($pre->type == 'redirect') {
            return redirect()->to(base_url($pre->file));
        } else {
            return $pre->data;
        }
    }



    /**
     * Make Angualer page integrable inside a Codeigniter page!
     *
     * @param string $module Name of module
     * @param string $returnPath Controller path or routing, for example: api/users
     * @param array  $arguments Array of arguments to pass to Angular.
     *               All arguments can be accessed from within the Angular app using
     *               document.getElementsByTagName("base")[0]. getAttribute("name")
     * @return void
     */
    protected function aopModularize(string $module, string $returnPath, array $arguments = []): string | \CodeIgniter\HTTP\RedirectResponse
    {
        $pre = $this->aopPreRender($module, $returnPath, $arguments);
        if ($pre->type == 'redirect') {
            return redirect()->to(base_url($pre->file));
        } else {
            $file = $pre->data;
            $matches = [];
            preg_match('/<body.*?>(.*?)<\/body>/s', $file, $matches);
            // Print the entire match result
            $body = $matches[1];
            $matches = [];
            preg_match('/<head.*?>(.*?)<\/head>/s', $file, $matches);
            // Print the entire match result
            $head = $matches[1];
            // Destructure Angular header
            $doc = new \DOMDocument();
            $doc->loadHTML('<html><head>' . $head . '</head></html>');
            foreach ($doc->getElementsByTagName('base') as $node) {
                $val = $doc->saveXML($node);
                $this->addHeadFooter($val, 'row', 'head');
            }
            foreach ($doc->getElementsByTagName('link') as $node) {
                $val = $doc->saveXML($node);
                if (strpos($val, 'rel="icon"') == false) {
                    echo $val . PHP_EOL;
                }

                $this->addHeadFooter($val, 'row', 'head');
            }
            foreach ($doc->getElementsByTagName('script') as $node) {
                $val = $doc->saveXML($node);
                $this->addHeadFooter($val, 'row', 'head');
            }
            foreach ($doc->getElementsByTagName('noscript') as $node) {
                $val = $doc->saveXML($node);
                $this->addHeadFooter($val, 'row', 'head');
            }
            foreach ($doc->getElementsByTagName('style') as $node) {
                $val = $doc->saveXML($node);
                $this->addHeadFooter($val, 'row', 'head');
            }

            // Return Body content
            return $body;
        }
    }

    /**
     * Summary of replaceOnEnv replace a value instide Env
     * @param mixed $par complete env path 
     * @param mixed $value value to write
     * @param mixed $file the whole string .env file 
     * @return array|null|string
     */
    protected function replaceOnEnv($par, $value, $file)
    {
        if (is_string($value)) {
            $value = str_replace("'", "\'", trim($value, "'"));
            $value = str_replace('"', '\"', trim($value, '"'));
            $value = str_replace("\n", " ", $value);
            $value = "'" . $value . "'";
        }

        if (is_bool($value))
            $value = ($value) ? 'true' : 'false';

        if ($value == null) return $file;

        $quoted = str_replace('.', '\.', $par);
        $re = '/.*?' . $quoted . '.*?\= (.*)/m';
        $file = preg_replace($re, $par." = ".str_replace(['$'],['\$'],$value), $file);
        $re = '/' . $quoted . '.*?\= (.*)/m';
        preg_match_all($re, $file, $matches, PREG_SET_ORDER, 0);
        if (empty($matches)) {
            $file .= "\n$par = ".$value;
        }
        return $file;
    }


    /**
     * Uncomplicated jwt Auth
     * 
     * @param string $user
     * @param string $token
     * @return object
     */
    protected function jwtDecode(string $token): object
    {
        
        $ret = (object)['error' => true, 'message' => '', 'decoded' => false];
        $key = getenv('emergency.jwt.key');
        if (empty($key)) {
            $ret->message = "no Jvt Key";
            return $ret;
        }
        try {
            $ret->decoded = JWT::decode($token, new Key($key, 'HS256'));
            $this->jwtInformation = $ret->decoded;
        } catch (Exception $ex) {
            $ret->decoded = false;
            $ret->message = $ex;
        }
        if(!empty($ret->decoded)){
            $ret->error = false;
            $ret->message = "Success";
        }
        return $ret;
    }


    /**
     * Uncomplicated jwtEncode Auth
     * 
     * @param array $additionalValues
     * @param mixed $key
     * @return string
     */
    protected function jwtEncode(array $additionalValues = [], $key = ''): string
    {
        if(empty($key)){
            $key = getenv('emergency.jwt.key');
            if(empty($key)){
                $key = bin2hex(random_bytes(32));
                file_put_contents(
                    APPPATH . '../.env', 
                    $this->replaceOnEnv(
                        'emergency.jwt.key', 
                        $key, 
                        file_get_contents(APPPATH . '../.env')
                    )
                );
            }
        }
        $iat = time(); // current timestamp value
        $exp = $iat + 3600;
 
        $payload = array(
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
        );
        $payload = array_merge($additionalValues, $payload);
         
        return JWT::encode($payload, $key, 'HS256');
    }

}
