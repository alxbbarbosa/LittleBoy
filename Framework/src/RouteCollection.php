<?php

namespace Abbarbosa\LittleBoy\Framework;

/**
 * ==============================================================================================================
 *
 * RouterCollection: Classe gerenciar coleções de rotas
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class RouteCollection
{
    private static $routes = array();
    private static $names  = array();

    private function __construct()
    {
        
    }

    private function __close()
    {
        
    }

    public static function add($method, $pattern, $callback, $name = null)
    {

        if (!in_array($method, ['get', 'post'])) {
            throw new \Exception("Metodo não impementado");
        }

        $pattern = implode('/', array_filter(explode('/', $pattern)));
        $uri     = $pattern;
        $pattern = '/^'.str_replace('/', '\/', $pattern).'$/';

        if (preg_match("/\{[A-Za-z0-9]{1,}\}/", $pattern)) {

            $pattern = preg_replace("/\{[A-Za-z0-9]{1,}\}/", "[A-Za-z0-9]{1,}", $pattern);
        }

        self::$routes[$method][$pattern] = $callback;

        if (!is_null($name)) {
            self::$names[$name] = array($method, $pattern);
        }
    }

    public static function name($route, $data = [])
    {

        $pattern = self::$names[$route] ?? false;

        if ($pattern) {

            if (count($data) > 0) {
                $pattern = self::parse($pattern[1], $data);
            } else {
                $pattern = str_replace(["/^", "$/", "\\"], '', $pattern[1]);
            }
        }
        return $pattern;
    }

    protected static function parse($pattern, $data)
    {

        if (preg_match("/[A-Za-z0-9]{1,}/", $pattern, $maches)) {

            $pattern = explode('/', str_replace(["/^", "$/", "\\"], '', $pattern));
            $index   = 0;
            $result  = [];
            foreach ($pattern as $piece) {
                if (preg_match("/^[\[][A-Za-z0-9]{1,}/", $piece)) {
                    $result[] = $data[$index++];
                } else {
                    $result[] = $piece;
                }
            }
        }
        return implode('/', $result);
    }

    public static function find($method, $path)
    {

        $path = implode('/', array_filter(explode('/', $path)));

        foreach (self::$routes[$method] as $pattern => $callback) {

            if (preg_match($pattern, $path, $params)) {
                $data          = $params;
                $params        = explode('/', array_shift($params));
                $obj           = new \stdClass();
                $obj->path     = $path;
                $obj->params   = self::values($data, $pattern);
                $obj->callback = $callback;
                return $obj;
            }
        }
        return false;
    }

    protected static function values($data, $pattern)
    {

        $values = [];
        $val    = explode('/', array_shift($data));

        if (preg_match("/[A-Za-z0-9]{1,}/", $pattern, $maches)) {

            $pattern = explode('/', str_replace(["/^", "$/", "\\"], '', $pattern));
            $index   = 0;
            foreach ($pattern as $piece) {

                if (preg_match("/^[\[][A-Za-z0-9]{1,}/", $piece)) {

                    $values[] = $val[$index];
                }
                $index++;
            }
        }
        return $values;
    }
}