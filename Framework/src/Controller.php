<?php

namespace Abbarbosa\LittleBoy\Framework;

/**
 * ==============================================================================================================
 *
 * Controller: Classe para criar camada de controle
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class Controller
{
    protected  $request;
    protected  $model;

    public function __construct($model = null)
    {
        $this->model = $model;
        $this->request = request();
    }

    protected function view($view_file, $data = null)
    {
        view($view_file, $data);
    }
}