<?php

namespace Abbarbosa\LittleBoy\Framework;

use Abbarbosa\LittleBoy\Framework\WebCache;

/**
 * ==============================================================================================================
 *
 * View: Classe para gerar as views
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class View
{

    public function render($view_file, $data = [])
    {

        $view_file = str_replace('.', '/', $view_file);

        $filename = '../views/'.$view_file.'.php';

        if (!file_exists($filename)) {
            throw new \Exception("A view não pode ser renderizada. Arquivo <u>{$filename}</u> não encontrado.");
        }
        ob_start();
        /**
         * Gerar variáveis automaticamente
         */
        if (count($data) > 0) {
            foreach ($data as $k => $v) {
                ${$k} = $v;
            }
        }
        require_once $filename;
        ob_end_flush();
        session()->flush();
    }
}