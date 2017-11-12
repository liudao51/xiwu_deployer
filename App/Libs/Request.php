<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/25
 * Time: 03:06
 */

namespace App\Libs;


class Request
{
    public function get($name='')
    {
        $name = trim($name);

        if ($name == '')
            return $_GET;

        if (isset($_GET[$name]))
            return $_GET[$name];

        return null;
    }

    public function post($name='')
    {
        $name = trim($name);

        if ($name == '')
            return $_POST;

        if (isset($_POST[$name]))
            return $_POST[$name];

        return null;
    }

    public function cookie($name='')
    {
        $name = trim($name);

        if ($name == '')
            return $_COOKIE;

        if (isset($_COOKIE[$name]))
            return $_COOKIE[$name];

        return null;
    }
}