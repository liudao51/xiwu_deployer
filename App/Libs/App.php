<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/24
 * Time: 23:07
 */

namespace App\Libs;



include_once 'Functions.php';

class App
{
    public function __construct()
    {
        $this->psr4Loader();

        $this->env_init();

        $this->app_init();
    }

    private function env_init()
    {
        //var_dump(spl_autoload_functions());exit;
        session_start();

        $this->loadEnv();

        Config::init();

        $this->handleRequest();
    }

    private function app_init()
    {
        $request_uri = $_SERVER["REQUEST_URI"];
        $request_uri = trim($request_uri,'/');

        $controller_name = $method_name = '';
        $parrams = array();

        if ($request_uri != '')
        {
            $arr = explode('/',$request_uri);

            $controller_name = $arr[0];
            $method_name = isset($arr[1]) ? $arr[1] : '';
            $parrams = isset($arr[2]) ? array_slice($arr,2) : array();
        }

        if ($controller_name == '')
        {
            $controller_name = 'index';
        }

        $controller = '\\App\\Controllers\\'.ucfirst($controller_name).'Controller';

        if ($method_name == '')
        {
            $method_name = 'index';
        }

        $http_method = strtolower($_SERVER['REQUEST_METHOD']);
        $method = $http_method.ucfirst($method_name);

        if (!class_exists($controller))
        {
            return false;
        }
        $ctl = new $controller();

        $result = call_user_func_array(array($ctl,$method),$parrams);

        if (is_string($result))
        {
            echo $result;
            exit;
        }
        //$result = $ctl->$method($parrams);

    }

    private function psr4Loader(){
        // instantiate the loader
        // 初始化loader
        include_once 'Psr4AutoloaderClass.php';

        $loader = new \App\Psr4AutoloaderClass();

        // register the autoloader
        // 注册autoloader
        $loader->register();

        // register the base directories for the namespace prefix
        // 注册命名空间前缀的多个base目录
        $loader->addNamespace('App', APP_ROOT);
        //$loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/tests');
    }

    private function handleRequest()
    {
        // todo 处理$_POST $_GET $_COOKIE 等数据,防止注入
    }

    private function loadEnv()
    {
        $filePath = BASE_ROOT.'.env';

        // Read file into an array of lines with auto-detected line endings
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autodetect);

        foreach ($lines as $line) {
            // Disregard comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            // Only use non-empty lines that look like setters
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=',$line);

                putenv("$name=$value");
                $_ENV[$name] = $value;
            }
        }
    }
}