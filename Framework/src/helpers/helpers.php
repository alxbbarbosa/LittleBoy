<?php

use \Abbarbosa\LittleBoy\Framework\View;
use \Abbarbosa\LittleBoy\Framework\Router;
use \Abbarbosa\LittleBoy\Framework\Request;
use \Abbarbosa\LittleBoy\Framework\Response;
use \Abbarbosa\LittleBoy\Framework\Flash;
use \Abbarbosa\LittleBoy\Framework\Validate;

/**
 * ==============================================================================================================
 *
 * Helpers: funções auxiliares para construção de aplicativos
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
function view($view_file, $data = [])
{
    $view = new View();

    return $view->render($view_file, $data);
}

function request($key = null)
{
    $request = new Request();

    return !is_null($key) ? $request->get($key) : $request;
}

function dd($data)
{
    var_dump($data);
    die();
}

function route($name)
{
    if (func_num_args() > 1) {
        $args   = func_get_args();
        $name   = array_shift($args);
        $params = $args;
    } else {
        $params = [];
    }
    return router()->name($name, $params);
}

function router($uri = null, $method = 'get')
{
    $router = new Router();

    if ($uri) {
        return $router->find($method, $uri);
    }
    return $router;
}

function response()
{
    return new Response;
}

function session()
{
    return new Flash;
}

function validate()
{
    return new Validate;
}

function old($field)
{
    return session()->getOld($field);
}
