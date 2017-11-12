<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/25
 * Time: 16:27
 */

namespace App\Libs;


class Response
{
    public function redirect($to)
    {
        header('location: '.$to);
        exit();
    }

    public function setCookie(Cookie $cookie)
    {
        setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());

        return $this;
    }

}