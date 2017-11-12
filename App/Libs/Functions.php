<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/25
 * Time: 02:31
 */

function env($key,$default=null)
{
    $value = getenv($key);

    if ($value === false) {
        return $value instanceof Closure ? $value() : $value;;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;

        case 'empty':
        case '(empty)':
            return '';

        case 'null':
        case '(null)':
            return null;
    }

    $value = trim($value,'"');

    return $value;
}

define('VIEW_ROOT',BASE_ROOT.'Resource/views/');

function view($file_name,$data=array())
{
    $full_filename = VIEW_ROOT.$file_name.'.html';

    if (!file_exists($full_filename))
    {
        return '';
    }

    require_once BASE_ROOT.'vendor/Twig/Autoloader.php';
    Twig_Autoloader::register();
    $loader = new Twig_Loader_Filesystem(VIEW_ROOT);
    $twig = new Twig_Environment($loader, array(
        //'cache' => BASE_ROOT.'Storage/Cache/Template',
    ));
    $template = $twig->loadTemplate($file_name.'.html');

    echo $template->render($data);

    return '';

}

function cookie($name = null, $value = null, $minutes = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
{
    $cookie = new \App\Libs\Cookie($name,$value,$minutes,$path,$domain,$secure,$httpOnly);

    return $cookie;
}

// 日志
function mlog($msg, $mlogfile = ''){
    $mpath = BASE_ROOT . 'Storage/Logs/';

    if (!is_dir($mpath)){
        mkdir($mpath,0777,true);
    }
    if ($mlogfile == ''){
        $mlogfile = $mpath . 'mlog.log';
    }else{
        $mlogfile = $mpath .$mlogfile;
    }
    if (is_object($msg) || is_array($msg)) {
        $msg = print_r($msg, true);
    }

    if (isset($msg) && $msg != '') {
        //$msg = preg_replace("/'/is","\\'", $msg);
    } else {
        $msg = '';
    }
    $mtime = date('Y-m-d H:i:s');
    $fp = fopen($mlogfile, 'a+');
    fwrite($fp, sprintf("%s\r\n", $mtime . '   ' . $msg));
    fclose($fp);
}