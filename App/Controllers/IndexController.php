<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/25
 * Time: 02:20
 */

namespace App\Controllers;


class IndexController extends BaseController
{
    public function getIndex()
    {

        return view('index',array());
    }
}