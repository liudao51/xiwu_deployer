<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/25
 * Time: 02:57
 */

namespace App\Controllers;


use App\Libs\Request;
use App\Libs\Response;

class BaseController
{
    protected $response;
    protected $request;

    public function __construct()
    {
        $this->response = new Response();
        $this->request = new Request();
    }
}