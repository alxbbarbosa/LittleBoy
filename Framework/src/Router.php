<?php

namespace Abbarbosa\LittleBoy\Framework;

use Abbarbosa\LittleBoy\Framework\RouteCollection;

/**
 * ==============================================================================================================
 *
 * Router: Classe roteadora
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class Router
{
    protected $route;

    public function __construct()
    {
        $route = null;
    }

    public function post($pattern, $callback)
    {
        if (is_array($pattern)) {

            if (array_key_exists('route', $pattern) && array_key_exists('as', $pattern)) {
                $this->add('post', $pattern['route'], $callback, $pattern['as']);
            } else {
                throw new \Exception("Declaração de rota está incorreta");
            }
        } else {
            $this->add('post', $pattern, $callback, null);
        }
        return $this;
    }

    public function get($pattern, $callback)
    {
        if (is_array($pattern)) {
            if (array_key_exists('route', $pattern) && array_key_exists('as', $pattern)) {
                $this->add('get', $pattern['route'], $callback, $pattern['as']);
            } else {
                throw new \Exception("Declaração de rota está incorreta");
            }
        } else {
            $this->add('get', $pattern, $callback, null);
        }
        return $this;
    }

    public function add($method, $pattern, $callback, $name = null)
    {

        RouteCollection::add($method, $pattern, $callback, $name);
    }

    public function name($route, $data = [])
    {

        $result = RouteCollection::name($route, $data);
        return $result ? request()->url().$result : false;
    }

    public function find($method, $request)
    {

        if (!is_string($request)) {
            $request = $request->uriAsString();
        }

        $find = RouteCollection::find($method, $request);

        if ($find) {
            $this->route = $find;
            return $this;
        }
        return $this->route = false;
    }

    public function path()
    {

        return request()->join($this->route->path);
    }

    public function execute()
    {

        if ($this->route == null) {
            return false;
        }

        try {
            if (is_callable($this->route->callback)) {
                return call_user_func_array($this->route->callback, array_values($this->route->params));
            } else {
                $call = explode("@", $this->route->callback);

                if (count($call) == 2) {
                    $controller = "App\\Controllers\\".$call[0];
                    $controller = new $controller;
                    $method     = $call[1];
                    return call_user_func_array(array($controller, $method), array_values($this->route->params));
                } else {
                    throw new Exception("Declaração de rota incorreta");
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}