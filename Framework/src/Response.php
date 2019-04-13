<?php

namespace Abbarbosa\LittleBoy\Framework;

/**
 * ==============================================================================================================
 *
 * Response: Classe para tratar retorno
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class Response
{

    public function __construct()
    {
        
    }

    public function back($old = false)
    {
        if ($old == true) {

            foreach ((array) request()->all() as $key => $value) {
                session()->setOld($key, $value);
            }
        }

        return header('Location: '.$_SERVER['HTTP_REFERER']);
    }

    public function redirect($uri = null, $old = false)
    {

        if ($old == true) {

            foreach ((array) request()->all() as $key => $value) {
                session()->setOld($key, $value);
            }
        }

        if (is_null($uri)) {
            $uri = request()->url();
        }
        return header("Location:{$uri}");
    }

    public function json($data)
    {

        if (!is_array($data)) {
            $data = (array) $data;
        }

        return json_encode($data);
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
}