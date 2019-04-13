<?php

namespace Abbarbosa\LittleBoy\Framework;

/**
 * ==============================================================================================================
 *
 * Flash: Classe para tratar as sessões
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class Flash
{

    public function error($message)
    {

        $_SESSION['errors'][] = $message;
    }

    public function flash($key, $message)
    {

        if (isset($_SESSION['flash'][$key])) {
            $_SESSION['flash'][$key] = $message;
        }
    }

    public function has($key)
    {
        return isset($_SESSION['flash'][$key]);
    }

    public function anyError()
    {
        return (isset($_SESSION['errors']) && count($_SESSION['errors']) > 0) ? true : false;
    }

    public function get($key)
    {
        if ($this->has($key)) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
    }

    public function errors()
    {
        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
            return $errors;
        }
    }

    public function setOld($field, $value)
    {

        $_SESSION['old'][$field] = $value;
    }

    public function getOld($field)
    {

        if (isset($_SESSION['old'][$field])) {
            $old = $_SESSION['old'][$field];
            unset($_SESSION['old'][$field]);
            return $old;
        }
    }

    public function flush()
    {
        unset($_SESSION['flash']);
        unset($_SESSION['errors']);
        unset($_SESSION['old']);
    }
}