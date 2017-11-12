<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/24
 * Time: 23:10
 */

namespace App\Libs;

class Config
{
    private static $configs = array();

    public static function init()
    {
        $config_path = BASE_ROOT.'Configs/';

        if (!is_dir($config_path))
            return ;

        if (@$handle = opendir($config_path)) {
            /* 这是正确地遍历目录方法 */
            while (false !== ($file = readdir($handle)))
            {
                if ($file == "." || $file == "..")
                    continue;

                if (strpos($file,'.php') === false)
                    continue;

                $name = str_replace('.php','',$file);


                self::$configs[$name] = include_once $config_path.$file;
            }
        }

        return ;
    }

    public static function get($name='')
    {
        $name = trim($name);

        $configs = self::$configs;

        if ($name == '')
        {
            return $configs;
        }

        if (isset($configs[$name]))
        {
            return $configs[$name];
        }

        return array();
    }
}